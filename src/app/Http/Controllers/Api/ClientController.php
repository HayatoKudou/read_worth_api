<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Client;
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
            'purchaseLimit' => $client->purchase_limit,
            'purchaseLimitUnit' => $client->purchase_limit_unit,
            'privateOwnershipAllow' => (boolean) $client->private_ownership_allow,
            'users' => count($client->users),
            'books' => count($client->books),
        ]]);
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $client = User::find(Auth::id())->client;
        $client->update([
            'name' => $request->get('name'),
            'purchase_limit' => $request->get('purchase_limit'),
            'purchase_limit_unit' => $request->get('purchase_limit_unit'),
            'private_ownership_allow' => $request->get('private_ownership_allow'),
        ]);
        return response()->json(['client' => $client], 201);
    }

    public function create(CreateRequest $request): JsonResponse
    {
        $client = $request->createClient();
        $client = Client::create([
            'name' => $client->name,
        ]);
        return response()->json(['client' => $client], 201);
    }
}
