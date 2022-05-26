<?php

namespace App\Http\Controllers\Api;

use App\Models\BookCategory;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookCategory\StoreRequest;

class BookCategoryController extends Controller
{
    public function create(string $clientId, StoreRequest $request): JsonResponse
    {
        $bookCategory = $request->makePost();
        BookCategory::create([
            'client_id' => $clientId,
            'name' => $bookCategory->name,
        ]);
        return response()->json();
    }
}
