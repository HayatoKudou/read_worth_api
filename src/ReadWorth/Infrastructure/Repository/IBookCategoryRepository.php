<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain\BookCategory;

interface IBookCategoryRepository
{
    public function store(BookCategory $bookCategory): void;
}
