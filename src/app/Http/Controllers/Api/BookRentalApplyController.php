<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use ReadWorth\Infrastructure\EloquentModel\Book;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\BookRentalApply\CreateRequest;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookHistory;

class BookRentalApplyController extends Controller
{
    public function create(string $workspaceId, string $bookId, CreateRequest $request): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);

            return DB::transaction(function () use ($request, $workspaceId, $bookId): JsonResponse {
                $book = Book::find($bookId);
                $book->update(['status' => Book::STATUS_CAN_NOT_LEND]);
                $bookRentalApply = $request->createBookRentalApply();
                $bookRentalApply::create([
                    'user_id' => Auth::id(),
                    'workspace_id' => $workspaceId,
                    'book_id' => $bookId,
                    'reason' => $request->get('reason'),
                    'rental_date' => Carbon::now(),
                    'expected_return_date' => Carbon::parse($request->get('expected_return_date')),
                ]);
                BookHistory::create([
                    'book_id' => $book->id,
                    'user_id' => Auth::id(),
                    'action' => 'lend book',
                ]);
                return response()->json([], 201);
            });
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }
}
