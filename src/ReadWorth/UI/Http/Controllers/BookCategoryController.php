<?php

namespace ReadWorth\UI\Http\Controllers;

use Illuminate\Http\JsonResponse;
use ReadWorth\UI\Http\Requests\CreateBookCategoryRequest;
use ReadWorth\UI\Http\Requests\DeleteBookCategoryRequest;
use ReadWorth\UI\Http\Resources\CreateBookCategoryResource;
use ReadWorth\UI\Http\Resources\DeleteBookCategoryResource;
use ReadWorth\Application\UseCase\BookCategories\CreateBookCategory;
use ReadWorth\Application\UseCase\BookCategories\DeleteBookCategory;

class BookCategoryController extends Controller
{
    public function __construct(
        private readonly CreateBookCategory $createBookCategoryUseCase,
        private readonly DeleteBookCategory $deleteBookCategoryUseCase
    ) {
    }

    public function create(CreateBookCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $this->createBookCategoryUseCase->create(new CreateBookCategoryResource(
            workspaceId: $request->route('workspaceId'),
            name: $validated['name']
        ));
        return response()->json([], 201);
    }

    public function delete(DeleteBookCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $this->deleteBookCategoryUseCase->delete(new DeleteBookCategoryResource(
            workspaceId: $request->route('workspaceId'),
            name: $validated['name']
        ));
        return response()->json();
    }
}
