<?php

namespace ReadWorth\UI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Application\UseCase\Books\GetBooks;
use ReadWorth\UI\Http\Requests\CreateBookRequest;
use ReadWorth\UI\Http\Requests\DeleteBookRequest;
use ReadWorth\UI\Http\Requests\UpdateBookRequest;
use Illuminate\Auth\Access\AuthorizationException;
use ReadWorth\Application\UseCase\Books\CreateBook;
use ReadWorth\Application\UseCase\Books\DeleteBook;
use ReadWorth\Application\UseCase\Books\ReturnBook;
use ReadWorth\Application\UseCase\Books\UpdateBook;
use ReadWorth\UI\Http\Resources\CreateBookResource;
use ReadWorth\UI\Http\Resources\DeleteBookResource;
use ReadWorth\UI\Http\Resources\ReturnBookResource;
use ReadWorth\UI\Http\Resources\UpdateBookResource;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookHistory;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BookController extends Controller
{
    public function __construct(
        private readonly GetBooks $getBooksUseCase,
        private readonly CreateBook $createBookUseCase,
        private readonly UpdateBook $updateBookUseCase,
        private readonly DeleteBook $deleteBookUseCase,
        private readonly ReturnBook $returnBookUseCase,
    ) {
    }

    public function list(string $workspaceId): JsonResponse
    {
        return response()->json($this->getBooksUseCase->get($workspaceId));
    }

    public function create(CreateBookRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $this->createBookUseCase->create(new CreateBookResource(
            workspaceId: $request->route('workspaceId'),
            category: $validated['category'],
            title: $validated['title'],
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
            title: $validated['title'],
            description: $validated['description'],
            image: $validated['image'],
            url: $validated['url']
        ));
        return response()->json();
    }

    public function delete(DeleteBookRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $this->deleteBookUseCase->delete(new DeleteBookResource(
            workspaceId: $request->route('workspaceId'),
            bookIds: $validated['bookIds']
        ));
        return response()->json();
    }

    public function return(int $workspaceId, int $bookId): JsonResponse
    {
        $this->returnBookUseCase->return(new ReturnBookResource(
            workspaceId: $workspaceId,
            bookId: $bookId
        ));
        return response()->json([]);
    }

    public function csvBulkCreate(int $workspaceId, Request $request): JsonResponse
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
