<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Client\CreateRequest;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use App\Models\Belonging;
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

    public function update(string $clientId, UpdateRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $client = Client::find($clientId);
            $client->update(['name' => $validated['name']]);
            return response()->json(['client' => $client], 201);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function create(CreateRequest $request)
    {
        try {
            $validated = $request->validated();
            $plan = Plan::where('name', 'free')->first();
            $client = Client::create([
                'plan_id' => $plan->id,
                'name' => $validated['name'],
            ]);
            $role = Role::create([
                'is_account_manager' => 1,
                'is_book_manager' => 1,
                'is_client_manager' => 1,
            ]);
            Belonging::create([
                'user_id' => Auth::id(),
                'client_id' => $client->id,
                'role_id' => $role->id,
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }
}
