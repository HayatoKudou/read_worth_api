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
use ReadWorth\Application\UseCase\UpdateBook;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\UI\Http\Requests\CreateBookRequest;
use ReadWorth\UI\Http\Requests\DeleteBookRequest;
use ReadWorth\UI\Http\Requests\UpdateBookRequest;
use Illuminate\Auth\Access\AuthorizationException;
use ReadWorth\UI\Http\Resources\CreateBookResource;
use ReadWorth\UI\Http\Resources\UpdateBookResource;
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
        private readonly CreateBook $createBookUseCase,
        private readonly UpdateBook $updateBookUseCase,
    ) {
    }

    public function list(string $workspaceId): JsonResponse
    {
        return response()->json($this->fetchBooksUseCase->fetch($workspaceId));
    }

    public function create(CreateBookRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $this->createBookUseCase->create(new CreateBookResource(
            workspaceId: $request->route('workspaceId'),
            category: $validated['category'],
            title: $validated['category'],
            description: $validated['description'],
            image: $validated['image'],
            url: $validated['url']
        ));
        return response()->json([], 201);
    }

    public function update(UpdateBookRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $this->updateBookUseCase->update(new UpdateBookResource(
            id: $validated['id'],
            workspaceId: $request->route('workspaceId'),
            category: $validated['category'],
            status: $validated['status'],
            title: $validated['category'],
            description: $validated['description'],
            image: $validated['image'],
            url: $validated['url']
        ));
        return response()->json();
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
