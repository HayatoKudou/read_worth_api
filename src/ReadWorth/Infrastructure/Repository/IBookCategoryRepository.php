<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain\BookCategory;

interface IBookCategoryRepository
{
    public function store(BookCategory $bookCategory): void;

    public function delete(BookCategory $bookCategory): void;
}
