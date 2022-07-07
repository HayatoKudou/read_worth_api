<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\Client;
use App\Models\BookHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\BookRentalApply\StoreRequest;

class BookRentalApplyController extends Controller
{
    public function create(string $clientId, string $bookId, StoreRequest $request): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);

            return DB::transaction(function () use ($request, $clientId, $bookId): JsonResponse {
                $book = Book::find($bookId);
                $book->update(['status' => Book::STATUS_CAN_NOT_LEND]);
                $bookRentalApply = $request->createBookRentalApply();
                $bookRentalApply::create([
                    'user_id' => Auth::id(),
                    'client_id' => $clientId,
                    'book_id' => $bookId,
                    'reason' => $request->reason,
                    'rental_date' => Carbon::now(),
                    'expected_return_date' => Carbon::parse($request->expected_return_date),
                ]);
                BookHistory::create([
                    'book_id' => $book->id,
                    'user_id' => Auth::id(),
                    'action' => 'return book',
                ]);
                return response()->json([], 201);
            });
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }
}
