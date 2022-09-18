<?php

namespace App\Http\Controllers\Api;

use App\Models\BookCategory;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookCategory\CreateRequest;

class BookCategoryController extends Controller
{
    public function create(string $workspaceId, CreateRequest $request): JsonResponse
    {
        $validated = $request->createBookCategory();
        BookCategory::create([
            'workspace_id' => $workspaceId,
            'name' => $validated->name,
        ]);
        $bookCategories = BookCategory::organization($workspaceId)->get();
        return response()->json([
            'bookCategories' => $bookCategories->map(fn (BookCategory $bookCategory) => [
                'name' => $bookCategory->name,
        ]), 201, ]);
    }
}
