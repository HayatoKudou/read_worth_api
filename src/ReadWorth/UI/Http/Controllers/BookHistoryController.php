<?php

namespace ReadWorth\UI\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use ReadWorth\Application\UseCase\BookHistories\GetBookHistories;

class BookHistoryController extends Controller
{
    public function __construct(
        private readonly GetBookHistories $getBookHistories
    ) {
    }

    public function list(int $workspaceId, int $bookId): JsonResponse
    {
        return response()->json($this->getBookHistories->get($workspaceId, $bookId));
    }
}
