<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Client\StoreRequest;

class ClientController
{
    public function create(StoreRequest $request): JsonResponse
    {
        $client = $request->makePost();
        $client = Client::create([
            'name' => $client->name,
        ]);
        return response()->json(['client' => $client], 201);
    }
}
