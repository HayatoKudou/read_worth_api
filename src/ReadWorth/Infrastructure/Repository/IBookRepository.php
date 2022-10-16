<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain\Book;
use ReadWorth\Domain\BookCategory;

interface IBookRepository
{
    public function store(Book $book, BookCategory $bookCategory): void;
}
