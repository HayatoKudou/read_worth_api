<?php

namespace ReadWorth\Domain\ValueObjects;

class BookId
{
    private int $bookId;

    public function __construct(int $bookId = null)
    {
        if (!$bookId) {
            $bookId = time() + \Auth::id();
        }
        $this->bookId = $bookId;
    }

    public function getBookId(): int
    {
        return $this->bookId;
    }
}
