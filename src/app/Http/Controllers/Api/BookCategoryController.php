<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use App\Http\Requests\BookCategory\StoreRequest;
use App\Models\Client;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
