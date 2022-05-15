<?php

namespace App\Http\Controllers\Api;

use App\Models\Client;
use App\Http\Requests\Client\StoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClientController
{
    public function create(StoreRequest $request): JsonResponse
    {
        $client = $request->makePost();
        Client::create([
            'name' => $client->name,
        ]);
        return response()->json();
    }
}
