<?php

namespace ReadWorth\Application\UseCase\Books;

use ReadWorth\Infrastructure\Repository\BookRepository;

class DeleteBookImage
{
    public function __construct(private readonly BookRepository $bookRepository)
    {
    }

    public function delete(int $bookId): void
    {
        $book = $this->bookRepository->findById($bookId);

        if ($book->image_path) {
            \Storage::delete($book->image_path);
        }
    }
}
