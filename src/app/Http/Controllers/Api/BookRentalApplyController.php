<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\BookRentalApply\StoreRequest;
use Illuminate\Support\Facades\DB;

class BookRentalApplyController extends Controller
{
    public function create(string $clientId, string $bookId, StoreRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);

            return DB::transaction(function () use ($request, $clientId, $bookId): JsonResponse {
                $user = User::find(Auth::id());
                $book = Book::find($bookId);
                $book->update(['status' => Book::STATUS_CAN_NOT_LEND]);
                $bookRentalApply = $request->createBookRentalApply();
                $bookRentalApply::create([
                    'user_id' => $user->id,
                    'client_id' => $clientId,
                    'book_id' => $bookId,
                    'reason' => $request->reason,
                    'rental_date' => Carbon::now(),
                    'expected_return_date' => Carbon::parse($request->expected_return_date),
                ]);
                return response()->json([], 201);
            });
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }
}
