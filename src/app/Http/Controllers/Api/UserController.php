<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Http\Requests\User\StoreRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function me(): JsonResponse
    {
        $user = User::find(Auth::id());
        return response()->json(['user' => [
            'clientId'  => $user->client_id,
            'name'  => $user->name,
            'email'  => $user->email,
            'apiToken'  => $user->api_token,
        ]]);
    }

    public function list()
    {
        $this->authorize('store', [Client::class, $community]);
        $users = User::all();
    }

    public function create(StoreRequest $request): JsonResponse
    {
        $user = $request->makePost();
        User::create([
            'client_id' => $request->get('clientId'),
            'name' => $user->name,
            'email' => $user->email,
            'password' => Hash::make($user->password),
            'api_token' => Str::random(60)
        ]);
        return response()->json();
    }
}
