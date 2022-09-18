<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\Role;
use App\Models\Belonging;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Client\CreateRequest;
use App\Http\Requests\Client\UpdateRequest;
use Illuminate\Auth\Access\AuthorizationException;

class ClientController extends Controller
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
            $validated = $request->validated();
            $workspace = Workspace::find($workspaceId);
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
