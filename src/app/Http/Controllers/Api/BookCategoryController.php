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
    public function __construct(private readonly BookCategoryService $service)
    {
    }

    public function create(string $workspaceId, CreateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $bookCategory = new BookCategoryDomain(
            workspaceId: $workspaceId,
            name: $validated['name']
        );
        $this->service->create($bookCategory);
        return response()->json([], 201);
    }

    public function delete(string $workspaceId, DeleteRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $bookCategory = new BookCategoryDomain(
            workspaceId: $workspaceId,
            name: $validated['name']
        );
        $this->service->delete($bookCategory);
        return response()->json();
    }
}
