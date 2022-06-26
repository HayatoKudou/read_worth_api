<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Client\StoreRequest;

class ClientController
{
    public function info(): JsonResponse
    {
        $user = User::find(Auth::id());
        $client = Client::find($user->client_id);
        return response()->json(['client' => [
            'id' => $client->id,
            'name' => $client->name,
            'plan' => $client->plan->name,
            'users' => count($client->users),
            'books' => count($client->books),
        ]]);
    }

    public function create(StoreRequest $request): JsonResponse
    {
        $client = $request->createClient();
        $client = Client::create([
            'name' => $client->name,
        ]);
        return response()->json(['client' => $client], 201);
    }
}
