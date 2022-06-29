<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\User;
use App\Models\Client;
use App\Models\BookReview;
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
                    'createdAt' => Carbon::parse($book->created_at,)->format('Y年m月d日'),
                    'purchaseApplicant' => $book->purchaseApply?->user,
                    'rentalApplicant' => $book->rentalApply?->user,
                    'reviews' => collect($book->reviews)?->map(fn (BookReview $bookReview) => [
                        'rate' => $bookReview->rate,
                        'review' => $bookReview->review,
                        'reviewedAt' => Carbon::parse($bookReview->created_at)->format('Y年m月d日 H時i分'),
                        'reviewer' => $bookReview->user->name,
                    ]),
                ]),
                'bookCategories' => $bookCategories->map(fn (BookCategory $bookCategory) => [
                    'name' => $bookCategory->name,
                ]),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }

    public function update(string $clientId, UpdateRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $request->validated();
            $book = Book::find($request->get('id'));
            $bookCategory = BookCategory::where('name', $request->get('category'))->first();
            \Storage::delete($book->image_path);

            if (!$bookCategory) {
                return response()->json('一致するカテゴリが見つかりません', 500);
            }
            $imagePath = $request->get('image') ? $book->storeImage($request->get('image')) : null;
            $book->update([
                'client_id' => $clientId,
                'book_category_id' => $bookCategory->id,
                'status' => $request->get('status'),
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'image_path' => $imagePath,
            ]);
            return response()->json();
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }

    public function create(string $clientId, CreateRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $bookCategory = BookCategory::where('name', $request->get('bookCategoryName'))->firstOrFail();
            $book = $request->createBook();
            $imagePath = $request->get('image') ? $book->storeImage($request->get('image')) : null;
            Book::create([
                'client_id' => $clientId,
                'book_category_id' => $bookCategory->id,
                'status' => Book::STATUS_CAN_LEND,
                'title' => $book->title,
                'description' => $book->description,
                'image_path' => $imagePath,
            ]);
            return response()->json([], 201);
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }

    public function return(string $clientId, string $bookId, Request $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);

            return DB::transaction(function () use ($bookId): JsonResponse {
                $user = User::find(Auth::id());
                BookRentalApply::where('user_id', $user->id)->where('book_id', $bookId)->update(['return_date' => Carbon::now()]);
                Book::find($bookId)->update(['status' => Book::STATUS_CAN_LEND]);
                return response()->json([]);
            });
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
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
                    Book::find($bookId)?->delete();
                });
            });
            return response()->json([]);
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }
}
