<?php

namespace App\Http\Controllers\Api;

use App\Models\BookCategory;
use App\Http\Requests\BookCategory\StoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BookCategoryController
{
    public function create(StoreRequest $request): JsonResponse
    {
        $user = $request->makePost();
        BookCategory::create([
            'name' => $user->name,
            'email' => $user->email,
            'password' => Hash::make($user->password),
            'api_token' => Str::random(60)
        ]);
        return response()->json();
    }
}
