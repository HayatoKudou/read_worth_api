<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Client\UpdateRequest;

class ClientController
{
    public function info(string $clientId): JsonResponse
    {
        $client = Client::find($clientId);
        return response()->json(['client' => [
            'id' => $client->id,
            'name' => $client->name,
            'plan' => $client->plan->name,
            'users' => $client->users->count(),
            'books' => $client->books->count(),
        ]]);
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $client = User::find(Auth::id())->client;
        $client->update(['name' => $request->get('name')]);
        return response()->json(['client' => $client], 201);
    }
}
