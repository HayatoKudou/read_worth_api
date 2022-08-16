<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Client\UpdateRequest;

class ClientController
{
    public function info(): JsonResponse
    {
        $client = User::find(Auth::id())->client;
        return response()->json(['client' => [
            'id' => $client->id,
            'name' => $client->name,
            'plan' => $client->plan->name,
            'users' => count($client->users),
            'books' => count($client->books),
        ]]);
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $client = User::find(Auth::id())->client;
        $client->update(['name' => $request->get('name')]);
        return response()->json(['client' => $client], 201);
    }
}
