<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\User\StoreRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function me(string $clientId): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $user = User::find(Auth::id());
            Log::debug( $user);
            Log::debug( $user->role);
            return response()->json(['user' => [
                'clientId'  => $user->client_id,
                'name'  => $user->name,
                'email'  => $user->email,
                'apiToken'  => $user->api_token,
                'role' => [
                    'is_account_manager' => $user->role->is_account_manager,
                    'is_book_manager' => $user->role->is_book_manager,
                    'is_client_manager' => $user->role->is_client_manager,
                ]
            ]]);
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }

    public function list(string $clientId): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $users = User::organization($clientId)->get();
            return response()->json([
                'users' => $users->map(fn (User $user) => [
                    'clientId'  => $user->client_id,
                    'name'  => $user->name,
                    'email'  => $user->email,
                    'role' => [
                        'is_account_manager' => $user->role->is_account_manager,
                        'is_book_manager' => $user->role->is_book_manager,
                        'is_client_manager' => $user->role->is_client_manager,
                    ]
                ]),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }

    public function create(string $clientId, StoreRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $user = $request->makePost();
            DB::transaction(function () use ($user, $request, $clientId): void {
                 $user = User::create([
                    'client_id' => $clientId,
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => "",
                ]);
                Role::create([
                    'user_id' => $user->id,
                    'is_account_manager' => in_array('アカウント管理', $request->roles),
                    'is_book_manager' => in_array('書籍管理', $request->roles),
                    'is_client_manager' => in_array('組織管理', $request->roles),
                ]);
            });
            return response()->json();
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }
}
