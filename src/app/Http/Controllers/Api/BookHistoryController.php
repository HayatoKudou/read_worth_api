<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Book;
use App\Models\Client;
use App\Models\BookHistory;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;

class BookHistoryController extends Controller
{
    public function list(string $clientId, string $bookId): JsonResponse
    {
        try {
            $client = Client::find($clientId);
            $this->authorize('affiliation', $client);
            $bookHistories = Book::find($bookId)->histories;
            return response()->json([
                'histories' => $bookHistories->map(fn (BookHistory $bookHistory) => [
                  'userName' => $bookHistory->user->name,
                  'action' => $bookHistory->action,
                  'date' => Carbon::parse($bookHistory->created_at)->format('Y年m月d日'),
                ]),
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([], 402);
        }
    }
}
