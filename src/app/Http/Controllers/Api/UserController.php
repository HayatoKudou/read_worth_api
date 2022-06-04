<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\StoreRequest;
use Illuminate\Auth\Access\AuthorizationException;

class UserController extends Controller
{
    public function me(string $clientId): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $user = User::find(Auth::id());
            return response()->json(['user' => [
                'clientId' => $user->client_id,
                'name' => $user->name,
                'email' => $user->email,
                'apiToken' => $user->api_token,
                'role' => [
                    'is_account_manager' => $user->role->is_account_manager,
                    'is_book_manager' => $user->role->is_book_manager,
                    'is_client_manager' => $user->role->is_client_manager,
                ],
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
                    'clientId' => $user->client_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => [
                        'is_account_manager' => $user->role->is_account_manager,
                        'is_book_manager' => $user->role->is_book_manager,
                        'is_client_manager' => $user->role->is_client_manager,
                    ],
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
            $user = $request->createUser();
            DB::transaction(function () use ($user, $request, $clientId): void {
                $user = User::create([
                    'client_id' => $clientId,
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => '',
                ]);
                Role::create([
                    'user_id' => $user->id,
                    'is_account_manager' => in_array('アカウント管理', $request->roles, true),
                    'is_book_manager' => in_array('書籍管理', $request->roles, true),
                    'is_client_manager' => in_array('組織管理', $request->roles, true),
                ]);
            });
            return response()->json();
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }

    public function update(string $clientId, StoreRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $request->validated();
            DB::transaction(function () use ($request, $clientId): void {
                $user = User::where('client_id', $clientId)->first();
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                ]);
                Role::where('user_id', $user->id)->update([
                    'is_account_manager' => in_array('アカウント管理', $request->roles, true),
                    'is_book_manager' => in_array('書籍管理', $request->roles, true),
                    'is_client_manager' => in_array('組織管理', $request->roles, true),
                ]);
            });
            return response()->json();
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }
}
