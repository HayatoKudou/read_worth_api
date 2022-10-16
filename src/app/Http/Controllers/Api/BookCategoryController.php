<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookCategory\CreateRequest;
use App\Http\Requests\BookCategory\DeleteRequest;
use ReadWorth\Application\Service\BookCategoryService;
use ReadWorth\Domain\BookCategory as BookCategoryDomain;

class BookCategoryController extends Controller
{
    public function create(string $workspaceId, CreateRequest $request, BookCategoryService $service): JsonResponse
    {
        $validated = $request->validated();
        $bookCategory = new BookCategoryDomain(
            workspaceId: $workspaceId,
            name: $validated['name']
        );
        $service->create($bookCategory);
        return response()->json([], 201);
    }

    public function delete(string $workspaceId, DeleteRequest $request, BookCategoryService $service): JsonResponse
    {
        $validated = $request->validated();
        $bookCategory = new BookCategoryDomain(
            workspaceId: $workspaceId,
            name: $validated['name']
        );
        $service->delete($bookCategory);
        return response()->json();
    }
}
