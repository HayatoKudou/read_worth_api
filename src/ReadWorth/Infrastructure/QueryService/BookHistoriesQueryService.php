<?php

namespace ReadWorth\Infrastructure\QueryService;

use Carbon\Carbon;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\BookHistory;

class BookHistoriesQueryService
{
    public function getBookHistories(int $bookId): array
    {
        return Book::find($bookId)->histories->map(fn (BookHistory $bookHistory) => [
            'userName' => $bookHistory->user->name,
            'actionName' => $bookHistory->action,
            'createdAt' => Carbon::parse($bookHistory->created_at)->format('Y/m/d H:i:s'),
        ])->toArray();
    }
}
