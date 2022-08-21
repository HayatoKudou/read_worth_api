<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use App\Models\Client;
use App\Models\BookReview;
use Illuminate\Support\Str;
use App\Models\BookRentalApply;
use App\Models\BookPurchaseApply;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\DeleteRequest;
use App\Http\Requests\User\UpdateRequest;
use Illuminate\Auth\Access\AuthorizationException;

class UserController extends Controller
{
    public function me(string $clientId): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $user = Auth::user();
            $clients = $user->clients;
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'apiToken' => $user->api_token,
                'role' => [
                    'isAccountManager' => $user->role->is_account_manager,
                    'isBookManager' => $user->role->is_book_manager,
                    'isClientManager' => $user->role->is_client_manager,
                ],
                'clients' => $clients->map(function ($client) {
                    return [
                        'id' => $client->id,
                        'name' => $client->name,
                    ];
                }),
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
            $users = User::organization($clientId)->get();
            return response()->json([
                'users' => $users->map(fn (User $user) => [
                    'id' => $user->id,
                    'clientId' => $user->client_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => [
                        'isAccountManager' => $user->role->is_account_manager,
                        'isBookManager' => $user->role->is_book_manager,
                        'isClientManager' => $user->role->is_client_manager,
                    ],
                ]),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function create(string $clientId, CreateRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            DB::transaction(function () use ($request, $client): void {
                $user = User::create([
                    'client_id' => $client->id,
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                    'api_token' => Str::random(60),
                ]);
                Role::create([
                    'user_id' => $user->id,
                    'is_account_manager' => in_array('アカウント管理', $request->get('roles'), true),
                    'is_book_manager' => in_array('書籍管理', $request->get('roles'), true),
                    'is_client_manager' => in_array('組織管理', $request->get('roles'), true),
                ]);
            });
            return response()->json();
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function update(string $clientId, UpdateRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            return DB::transaction(function () use ($request): JsonResponse {
                $user = User::find($request->get('id'));

                if (!$user) {
                    return response()->json(['errors' => '一致するユーザーが見つかりません'], 500);
                }
                $user->update([
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                ]);
                Role::where('user_id', $user->id)->update([
                    'is_account_manager' => in_array('アカウント管理', $request->get('roles'), true),
                    'is_book_manager' => in_array('書籍管理', $request->get('roles'), true),
                    'is_client_manager' => in_array('組織管理', $request->get('roles'), true),
                ]);
                return response()->json();
            });
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function delete(string $clientId, DeleteRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            DB::transaction(function () use ($request): void {
                $request->collect('user_ids')->each(function ($userId): void {
                    BookPurchaseApply::where('user_id', $userId)->delete();
                    BookRentalApply::where('user_id', $userId)->delete();
                    BookReview::where('user_id', $userId)->delete();
                    Role::where('user_id', $userId)->delete();
                    User::find($userId)?->delete();
                });
            });
            return response()->json([]);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }
}
