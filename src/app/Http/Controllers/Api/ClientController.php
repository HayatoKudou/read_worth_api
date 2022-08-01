<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\User;
use App\Models\Client;
use App\Models\BookCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Client\CreateRequest;
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
            'enablePurchaseLimit' => (bool) $client->enable_purchase_limit,
            'purchaseLimit' => $client->purchase_limit,
            'purchaseLimitUnit' => $client->purchase_limit_unit,
            'privateOwnershipAllow' => (bool) $client->private_ownership_allow,
            'users' => count($client->users),
            'books' => count($client->books),
        ]]);
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $client = User::find(Auth::id())->client;
        $client->update([
            'name' => $request->get('name'),
            'enable_purchase_limit' => $request->get('enable_purchase_limit'),
            'purchase_limit' => $request->get('purchase_limit'),
            'purchase_limit_unit' => $request->get('purchase_limit_unit'),
            'private_ownership_allow' => $request->get('private_ownership_allow'),
        ]);
        return response()->json(['client' => $client], 201);
    }

    public function create(CreateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $plan = Plan::where('name', $validated['plan'])->first();

        if (!$plan) {
            return response()->json(['errors' => ['custom' => '一致するプランが存在しません']], 500);
        }

        $user = User::find($validated['userId']);

        if (!$user) {
            return response()->json(['errors' => ['custom' => '一致するユーザーが存在しません']], 500);
        }

        $client = Client::create([
            'name' => $validated['name'],
            'enable_purchase_limit' => false,
            'purchase_limit' => 0,
            'purchase_limit_unit' => 'monthly',
            'private_ownership_allow' => false,
            'plan_id' => $plan->id,
        ]);
        BookCategory::create([
            'client_id' => $client->id,
            'name' => 'ALL',
        ]);
        return response()->json([
            'me' => [
                'id' => $user->id,
                'clientId' => $user->client_id,
                'name' => $user->name,
                'email' => $user->email,
                'apiToken' => $user->api_token,
                'role' => [
                    'is_account_manager' => $user->role->is_account_manager,
                    'is_book_manager' => $user->role->is_book_manager,
                    'is_client_manager' => $user->role->is_client_manager,
                ],
            ],
        ], 201);
    }
}
