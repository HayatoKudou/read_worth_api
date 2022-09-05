<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Client\UpdateRequest;
use Illuminate\Auth\Access\AuthorizationException;

class ClientController extends Controller
{
    public function info(string $clientId): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            return response()->json([
                'id' => $client->id,
                'name' => $client->name,
                'plan' => $client->plan->name,
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function list(string $clientId): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $user = Auth::user();
            return response()->json(
                $user->clients->map(function (Client $client) {
                    return [
                        'id' => $client->id,
                        'name' => $client->name,
                        'plan' => $client->plan->name,
                    ];
                }),
            );
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        try {
            $client = User::find(Auth::id())->client;
            $client->update(['name' => $request->get('name')]);
            return response()->json(['client' => $client], 201);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }
}
