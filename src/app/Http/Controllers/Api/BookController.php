<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\Client;
use App\Models\BookReview;
use App\Models\BookHistory;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use App\Models\BookRentalApply;
use App\Models\BookPurchaseApply;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Book\CreateRequest;
use App\Http\Requests\Book\DeleteRequest;
use App\Http\Requests\Book\UpdateRequest;
use Illuminate\Auth\Access\AuthorizationException;

class BookController extends Controller
{
    public function list($clientId): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $books = Book::organization($clientId)->with('purchaseApply')->get();
            $bookCategories = BookCategory::organization($clientId)->get();
            return response()->json([
                'books' => $books->map(fn (Book $book) => [
                    'id' => $book->id,
                    'status' => $book->status,
                    'category' => $book->category->name,
                    'title' => $book->title,
                    'description' => $book->description,
                    'image' => $book->image_path ? base64_encode(Storage::get($book->image_path)) : null,
                    'url' => $book->url,
                    'createdAt' => Carbon::parse($book->created_at)->format('Y/m/d'),
                    'purchaseApplicant' => $book->purchaseApply?->user,
                    'rentalApplicant' => $book->rentalApply?->user,
                    'reviews' => collect($book->reviews)?->map(fn (BookReview $bookReview) => [
                        'rate' => $bookReview->rate,
                        'review' => $bookReview->review,
                        'reviewedAt' => Carbon::parse($bookReview->created_at)->format('Y年m月d日 H時i分'),
                        'reviewer' => $bookReview->user->name,
                    ]),
                    'rentalCount' => $book->rentalHistories->count(),
                ]),
                'bookCategories' => $bookCategories->map(fn (BookCategory $bookCategory) => [
                    'name' => $bookCategory->name,
                ]),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function create(string $clientId, CreateRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            DB::transaction(function () use ($clientId, $request): void {
                $bookCategory = BookCategory::where('name', $request->get('bookCategoryName'))->firstOrFail();
                $book = Book::create([
                    'client_id' => $clientId,
                    'book_category_id' => $bookCategory->id,
                    'status' => Book::STATUS_CAN_LEND,
                    'title' => $request->get('title'),
                    'description' => $request->get('description'),
                    'url' => $request->get('url'),
                ]);
                $book->update(['image_path' => $request->get('image') ? $book->storeImage($request->get('image')) : null]);
                BookHistory::create([
                    'book_id' => $book->id,
                    'user_id' => Auth::id(),
                    'action' => 'create book',
                ]);
            });
            return response()->json([], 201);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function update(string $clientId, UpdateRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $book = Book::find($request->get('id'));
            $bookCategory = BookCategory::where('name', $request->get('category'))->first();

            if ($book->image_path) {
                \Storage::delete($book->image_path);
            }

            if (!$bookCategory) {
                return response()->json('一致するカテゴリが見つかりません', 500);
            }
            $imagePath = $request->get('image') ? $book->storeImage($request->get('image')) : null;

            DB::transaction(function () use ($clientId, $bookCategory, $book, $request, $imagePath): void {
                if ($book->status != $request->get('status')) {
                    $action = 'other';
                    // 申請中 ⇨ 登録
                    if (Book::STATUS_APPLYING === $book->status && Book::STATUS_CAN_LEND === $request->get('status')) {
                        $action = 'create book';
                    // 貸出中 ⇨ 貸出可能
                    } elseif (Book::STATUS_CAN_NOT_LEND === $book->status && Book::STATUS_CAN_LEND === $request->get('status')) {
                        $action = 'return book';
                    // 貸出可能 ⇨ 貸出中
                    } elseif (Book::STATUS_CAN_LEND === $book->status && Book::STATUS_CAN_NOT_LEND === $request->get('status')) {
                        $action = 'lend book';
                    }
                    BookHistory::create([
                        'book_id' => $book->id,
                        'user_id' => Auth::id(),
                        'action' => $action,
                    ]);
                }
                $book->update([
                    'client_id' => $clientId,
                    'book_category_id' => $bookCategory->id,
                    'status' => $request->get('status'),
                    'title' => $request->get('title'),
                    'description' => $request->get('description'),
                    'image_path' => $imagePath,
                    'url' => $request->get('url'),
                ]);
            });

            return response()->json();
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function delete(string $clientId, DeleteRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            DB::transaction(function () use ($request): void {
                $request->collect('book_ids')->each(function ($bookId): void {
                    BookPurchaseApply::where('book_id', $bookId)->delete();
                    BookRentalApply::where('book_id', $bookId)->delete();
                    BookReview::where('book_id', $bookId)->delete();
                    BookHistory::where('book_id', $bookId)->delete();
                    Book::find($bookId)?->delete();
                });
            });
            return response()->json([]);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function return(string $clientId, string $bookId): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);

            DB::transaction(function () use ($bookId): void {
                BookRentalApply::where('user_id', Auth::id())->where('book_id', $bookId)->update(['return_date' => Carbon::now()]);
                Book::find($bookId)->update(['status' => Book::STATUS_CAN_LEND]);
                BookHistory::create([
                    'book_id' => $bookId,
                    'user_id' => Auth::id(),
                    'action' => 'return book',
                ]);
            });
            return response()->json([]);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function csvBulkCreate(string $clientId, Request $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            return DB::transaction(function () use ($request, $clientId): JsonResponse {
                foreach ($request->get('books') as $csvData) {
                    $bookCategory = BookCategory::where('name', $csvData['カテゴリ'])->first();

                    if (null === $bookCategory) {
                        return response()->json(['errors' => '登録されていないカテゴリが含まれています'], 500);
                    }
                    $book = Book::where('title', $csvData['タイトル'])->first();
                    $bookExists = Book::where('title', $csvData['タイトル'])->exists();
                    $imagePath = $csvData['URL'] ? Book::fetchAmazonImage(urldecode($csvData['URL'])) : null;

                    if ($bookExists) {
                        $book->update([
                            'client_id' => $clientId,
                            'book_category_id' => $bookCategory->id,
                            'status' => Book::STATUS_CAN_LEND,
                            'title' => $csvData['タイトル'],
                            'description' => $csvData['本の説明'],
                            'image_path' => $imagePath,
                            'url' => $csvData['URL'],
                        ]);
                    } else {
                        $book = Book::create([
                            'client_id' => $clientId,
                            'book_category_id' => $bookCategory->id,
                            'status' => Book::STATUS_CAN_LEND,
                            'title' => $csvData['タイトル'],
                            'description' => $csvData['本の説明'],
                            'image_path' => $imagePath,
                            'url' => $csvData['URL'],
                        ]);
                        BookHistory::create([
                            'book_id' => $book->id,
                            'user_id' => Auth::id(),
                            'action' => 'create book',
                        ]);
                    }
                }
                return response()->json([], 201);
            });
        } catch (AuthorizationException $e) {
            \Log::debug($e->getMessage());
            return response()->json([], 403);
        }
    }
}
