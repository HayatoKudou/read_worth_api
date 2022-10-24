<?php

namespace ReadWorth\UI\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use ReadWorth\Application\UseCase\CreateBook;
use ReadWorth\Application\UseCase\FetchBooks;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\UI\Http\Requests\CreateBookRequest;
use ReadWorth\UI\Http\Requests\DeleteBookRequest;
use ReadWorth\UI\Http\Requests\UpdateBookRequest;
use Illuminate\Auth\Access\AuthorizationException;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookReview;
use ReadWorth\Infrastructure\EloquentModel\BookHistory;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;
use ReadWorth\Infrastructure\EloquentModel\BookRentalApply;
use ReadWorth\Infrastructure\EloquentModel\BookPurchaseApply;

class BookController extends Controller
{
    public function __construct(
        private readonly FetchBooks $fetchBooksUseCase,
        private readonly CreateBook $createBookUseCase
    ) {
    }

    public function list(string $workspaceId): JsonResponse
    {
        return response()->json($this->fetchBooksUseCase->fetch($workspaceId));
    }

    public function create(CreateBookRequest $request): JsonResponse
    {
        $this->createBookUseCase->create($request);
        return response()->json([], 201);
    }

    public function update(string $workspaceId, UpdateBookRequest $request): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);
            $this->authorize('isBookManager', $workspace);
            $book = Book::find($request->get('id'));
            $bookCategory = BookCategory::where('name', $request->get('category'))->first();

            if (!$book) {
                abort(401);
            }

            if ($book->image_path) {
                \Storage::delete($book->image_path);
            }
            $imagePath = $request->get('image') ? $book->storeImage($request->get('image'), $workspaceId) : null;

            DB::transaction(function () use ($workspaceId, $bookCategory, $book, $request, $imagePath): void {
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
                    'workspace_id' => $workspaceId,
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

    public function delete(string $workspaceId, DeleteBookRequest $request): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);
            $this->authorize('isBookManager', $workspace);
            DB::transaction(function () use ($request): void {
                $request->collect('bookIds')->each(function ($bookId): void {
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

    public function return(string $workspaceId, string $bookId): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);

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

    public function csvBulkCreate(string $workspaceId, Request $request): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);
            $this->authorize('isBookManager', $workspace);
            return DB::transaction(function () use ($request, $workspaceId): JsonResponse {
                foreach ($request->get('books') as $csvData) {
                    $bookCategory = BookCategory::where('name', $csvData['カテゴリ'])->first();

                    if (null === $bookCategory) {
                        return response()->json(['errors' => '登録されていないカテゴリが含まれています'], 500);
                    }
                    $book = Book::where('title', $csvData['タイトル'])->first();
                    $bookExists = Book::where('title', $csvData['タイトル'])->exists();
                    $imagePath = $csvData['URL'] ? Book::fetchAmazonImage(urldecode($csvData['URL']), $workspaceId) : null;

                    if ($bookExists) {
                        $book->update([
                            'workspace_id' => $workspaceId,
                            'book_category_id' => $bookCategory->id,
                            'status' => Book::STATUS_CAN_LEND,
                            'title' => $csvData['タイトル'],
                            'description' => $csvData['本の説明'],
                            'image_path' => $imagePath,
                            'url' => $csvData['URL'],
                        ]);
                    } else {
                        $book = Book::create([
                            'workspace_id' => $workspaceId,
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
            return response()->json([], 403);
        }
    }
}
