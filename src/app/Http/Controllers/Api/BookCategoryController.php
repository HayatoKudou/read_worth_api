<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookCategory\CreateRequest;
use App\Http\Requests\BookCategory\DeleteRequest;
use ReadWorth\Application\Service\BookCategoryService;

class BookCategoryController extends Controller
{
    public function __construct(private readonly BookCategoryService $service)
    {
    }

    public function create(CreateRequest $request): JsonResponse
    {
        $this->service->create($request);
        return response()->json([], 201);
    }

    public function delete(DeleteRequest $request): JsonResponse
    {
        $this->service->delete($request);
        return response()->json();
    }
}
