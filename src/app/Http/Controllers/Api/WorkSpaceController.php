<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Workspace\CreateRequest;
use App\Http\Requests\Workspace\UpdateRequest;
use ReadWorth\Infrastructure\EloquentModel\Plan;
use ReadWorth\Infrastructure\EloquentModel\Role;
use Illuminate\Auth\Access\AuthorizationException;
use ReadWorth\Infrastructure\EloquentModel\Belonging;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class WorkSpaceController extends Controller
{
    public function info(string $workspaceId): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);
            return response()->json([
                'id' => $workspace->id,
                'name' => $workspace->name,
                'plan' => $workspace->plan->name,
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
            $user = Auth::user();
            return response()->json(
                $user->workspaces->map(function (Workspace $workspace) {
                    return [
                        'id' => $workspace->id,
                        'name' => $workspace->name,
                        'plan' => $workspace->plan->name,
                    ];
                }),
            );
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function update(string $workspaceId, UpdateRequest $request): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('isWorkspaceManager', $workspace);
            $validated = $request->validated();
            $workspace->update(['name' => $validated['name']]);
            return response()->json([], 201);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }

    public function create(CreateRequest $request)
    {
        try {
            $validated = $request->validated();
            $plan = Plan::where('name', 'free')->first();
            $workspace = Workspace::create([
                'plan_id' => $plan->id,
                'name' => $validated['name'],
            ]);
            BookCategory::create([
                'workspace_id' => $workspace->id,
                'name' => 'ALL',
            ]);
            $role = Role::create([
                'is_account_manager' => 1,
                'is_book_manager' => 1,
                'is_workspace_manager' => 1,
            ]);
            Belonging::create([
                'user_id' => Auth::id(),
                'workspace_id' => $workspace->id,
                'role_id' => $role->id,
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }
}
