<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BookReview\CreateRequest;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\User;
use Illuminate\Auth\Access\AuthorizationException;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookRentalApply;

class BookReviewController extends Controller
{
    public function create(string $workspaceId, string $bookId, CreateRequest $request): JsonResponse
    {
        try {
            $workspace = Workspace::find($workspaceId);
            $this->authorize('affiliation', $workspace);

            return DB::transaction(function () use ($request, $bookId): JsonResponse {
                $user = User::find(Auth::id());
                $bookReview = $request->createBookReview();
                $bookReview::create([
                    'user_id' => $user->id,
                    'book_id' => $bookId,
                    'rate' => $request->get('rate'),
                    'review' => $request->get('review'),
                ]);
                BookRentalApply::where('book_id', $bookId)->update(['rental_date' => Carbon::now()]);
                Book::find($bookId)->update(['status' => Book::STATUS_CAN_LEND]);
                return response()->json([], 201);
            });
        } catch (AuthorizationException $e) {
            return response()->json([], 403);
        }
    }
}
