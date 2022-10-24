<?php

namespace ReadWorth\UI\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use ReadWorth\Application\UseCase\CreateBookCategory;
use ReadWorth\Application\UseCase\DeleteBookCategory;
use ReadWorth\UI\Http\Requests\CreateBookCategoryRequest;
use ReadWorth\UI\Http\Requests\DeleteBookCategoryRequest;

class BookCategoryController extends Controller
{
    public function __construct(
        private readonly CreateBookCategory $createBookCategoryUseCase,
        private readonly DeleteBookCategory $deleteBookCategoryUseCase
    ) {
    }

    public function create(CreateBookCategoryRequest $request): JsonResponse
    {
        $this->createBookCategoryUseCase->create($request);
        return response()->json([], 201);
    }

    public function delete(DeleteBookCategoryRequest $request): JsonResponse
    {
        $this->deleteBookCategoryUseCase->delete($request);
        return response()->json();
    }
}
