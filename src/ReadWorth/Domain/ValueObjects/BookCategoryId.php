<?php

namespace ReadWorth\Domain\ValueObjects;

class BookCategoryId
{
    private int $bookCategoryId;

    public function __construct(int $bookCategoryId = null)
    {
        if(!$bookCategoryId) $bookCategoryId = time() + \Auth::id();
        $this->bookCategoryId = $bookCategoryId;
    }

    public function getBookCategoryId(): int
    {
        return $this->bookCategoryId;
    }
}
