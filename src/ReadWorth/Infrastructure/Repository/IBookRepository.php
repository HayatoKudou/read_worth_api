<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain;
use ReadWorth\Infrastructure\EloquentModel\Book;

interface IBookRepository
{
    public function store(Domain\Workspace $workspace, Domain\Book $book, Domain\BookCategory $bookCategory): void;

    public function update(Domain\Workspace $workspace, Domain\Book $book, Domain\BookCategory $bookCategory): void;

    public function findById(int $bookId): Book;
}
