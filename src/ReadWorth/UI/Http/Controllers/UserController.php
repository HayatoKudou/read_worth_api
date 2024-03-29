<?php

namespace ReadWorth\UI\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Requests\User\MeUpdateRequest;
use ReadWorth\Infrastructure\EloquentModel\Role;
use ReadWorth\Infrastructure\EloquentModel\User;
use Illuminate\Auth\Access\AuthorizationException;
use ReadWorth\Infrastructure\EloquentModel\Belonging;
use ReadWorth\Infrastructure\EloquentModel\Workspace;

class UserController extends Controller
{
    public function me(string $workspaceId): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);
            $user = Auth::user();
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'apiToken' => $user->api_token,
                'role' => [
                    'isAccountManager' => $user->role($workspaceId)->is_account_manager,
                    'isBookManager' => $user->role($workspaceId)->is_book_manager,
                    'isWorkspaceManager' => $user->role($workspaceId)->is_workspace_manager,
                ],
                'workspaces' => $user->workspaces->map(function ($workspace) {
                    return [
                        'id' => $workspace->id,
                        'name' => $workspace->name,
                    ];
                }),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function list(string $workspaceId): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);
            return response()->json([
                'users' => $workspace->users->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => [
                        'isAccountManager' => $user->role($workspaceId)->is_account_manager,
                        'isBookManager' => $user->role($workspaceId)->is_book_manager,
                        'isWorkspaceManager' => $user->role($workspaceId)->is_workspace_manager,
                    ],
                ]),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function create(string $workspaceId, CreateRequest $request): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);
            $this->authorize('isAccountManager', $workspace);
            $validated = $request->validated();
            return DB::transaction(function () use ($validated, $workspace): JsonResponse {
                $user = User::firstOrCreate([
                    'email' => $validated['email'],
                ], [
                    'name' => $validated['name'],
                    'api_token' => Str::random(60),
                ]);
                $role = Role::create([
                    'is_account_manager' => in_array('アカウント管理', $validated['roles'], true),
                    'is_book_manager' => in_array('書籍管理', $validated['roles'], true),
                    'is_workspace_manager' => in_array('ワークスペース管理', $validated['roles'], true),
                ]);

                if (Belonging::where('user_id', $user->id)->where('workspace_id', $workspace->id)->exists()) {
                    return response()->json(['errors' => ['email' => ['該当ユーザーはすでに登録されています。']]], 402);
                }
                Belonging::create([
                    'user_id' => $user->id,
                    'workspace_id' => $workspace->id,
                    'role_id' => $role->id,
                ]);
                return response()->json();
            });
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function update(string $workspaceId, UpdateRequest $request): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);
            $this->authorize('isAccountManager', $workspace);
            return DB::transaction(function () use ($request, $workspaceId): JsonResponse {
                $user = User::find($request->get('id'));
                $user->update([
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                ]);
                $user->role($workspaceId)->update([
                    'is_account_manager' => in_array('アカウント管理', $request->get('roles'), true),
                    'is_book_manager' => in_array('書籍管理', $request->get('roles'), true),
                    'is_workspace_manager' => in_array('ワークスペース管理', $request->get('roles'), true),
                ]);
                return response()->json();
            });
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function meUpdate(string $workspaceId, MeUpdateRequest $request): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $user = Auth::user();
            $this->authorize('affiliation', $workspace);

            if ($user->email !== $request->get('email')) {
                return response()->json([], 403);
            }

            return DB::transaction(function () use ($user, $request): JsonResponse {
                $user->update([
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                ]);
                return response()->json();
            });
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }
}
