<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use ReadWorth\Domain\BookCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookCategory\CreateRequest;
use App\Http\Requests\BookCategory\DeleteRequest;
use Illuminate\Auth\Access\AuthorizationException;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Application\Service\BookCategoryService;

class BookCategoryController extends Controller
{
    public function create(string $workspaceId, CreateRequest $request, BookCategoryService $service): JsonResponse
    {
        $validated = $request->validated();
        $bookCategory = new BookCategory(
            workspaceId: $workspaceId,
            name: $validated['name']
        );
        $service->create($bookCategory);
        return response()->json([], 201);
    }

    public function delete(string $workspaceId, DeleteRequest $request): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);
            $validated = $request->validated();

            // ALLカテゴリは削除させない
            if ('ALL' === $validated['name']) {
                return response()->json([], 422);
            }

            $bookCategory = BookCategory::where('name', $validated['name'])->firstOrFail();
            $bookCategory->books->each(function ($book) use ($workspaceId): void {
                $all = BookCategory::where('workspace_id', $workspaceId)->where('name', 'ALL')->firstOrFail();
                $book->update(['book_category_id' => $all->id]);
            });
            $bookCategory->delete();
            return response()->json();
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }
}
