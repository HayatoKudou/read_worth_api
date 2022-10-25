<?php

namespace ReadWorth\Application\UseCase;

use ReadWorth\Domain\IBookRepository;

class DeleteBookImage
{
    public function __construct(private readonly IBookRepository $bookRepository)
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
