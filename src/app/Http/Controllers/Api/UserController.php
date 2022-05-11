<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Requests\User\StoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController
{
    public function create(StoreRequest $request): JsonResponse
    {
        $user = $request->makePost();
        User::create([
            'name' => $user->name,
            'email' => $user->email,
            'password' => Hash::make($user->password),
            'api_token' => "aaaa",
        ]);
        return response()->json();
    }
}
