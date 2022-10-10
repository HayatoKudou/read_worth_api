<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use ReadWorth\Infrastructure\EloquentModel\Book;
use Illuminate\Auth\Access\AuthorizationException;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookHistory;

class BookHistoryController extends Controller
{
    public function list(string $workspaceId, string $bookId): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);
            $bookHistories = Book::find($bookId)->histories;
            return response()->json(
                $bookHistories->map(fn (BookHistory $bookHistory) => [
                  'userName' => $bookHistory->user->name,
                  'actionName' => $bookHistory->action,
                  'createdAt' => Carbon::parse($bookHistory->created_at)->format('Y/m/d H:i:s'),
                ]),
            );
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }
}
