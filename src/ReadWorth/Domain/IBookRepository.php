<?php

namespace ReadWorth\Domain;

use ReadWorth\Infrastructure\EloquentModel\Book;

interface IBookRepository
{
    public function store(Entities\Workspace $workspace, Entities\Book $book, Entities\BookCategory $bookCategory): void;

    public function update(Entities\Workspace $workspace, Entities\Book $book, Entities\BookCategory $bookCategory): void;

    public function findById(int $bookId): Book;
}
