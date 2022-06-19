<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function me(string $clientId): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $user = User::find(Auth::id());
            return response()->json(['user' => [
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
                    'id' => $user->id,
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

    public function create(string $clientId, CreateRequest $request): JsonResponse
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
                    'password' => Hash::make($request->get('password')),
                    'api_token' => Str::random(60),
                ]);
                event(new Registered($user));
                Role::create([
                    'user_id' => $user->id,
                    'is_account_manager' => in_array('アカウント管理', $request->get('roles'), true),
                    'is_book_manager' => in_array('書籍管理', $request->get('roles'), true),
                    'is_client_manager' => in_array('組織管理', $request->get('roles'), true),
                ]);
            });
            return response()->json();
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }

    public function update(string $clientId, UpdateRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $request->validated();
            return DB::transaction(function () use ($request): JsonResponse {
                $user = User::find($request->get('id'));

                if (!$user) {
                    return response()->json('一致するユーザーが見つかりません', 500);
                }
                $user->update([
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                ]);

                if ($request->get('password')) {
                    $user->update(['password' => Hash::make($request->get('password'))]);
                }
                Role::where('user_id', $user->id)->update([
                    'is_account_manager' => in_array('アカウント管理', $request->get('roles'), true),
                    'is_book_manager' => in_array('書籍管理', $request->get('roles'), true),
                    'is_client_manager' => in_array('組織管理', $request->get('roles'), true),
                ]);
                return response()->json();
            });
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }
}
