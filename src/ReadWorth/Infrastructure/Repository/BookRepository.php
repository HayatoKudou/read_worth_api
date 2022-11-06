<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain\Entities;
use Illuminate\Support\Facades\DB;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\BookHistory;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BookRepository
{
    public function store(Entities\Workspace $workspace, Entities\Book $book, Entities\User $user): void
    {
        $bookCategoryRepo = BookCategory::where('workspace_id', $workspace->getId())
            ->where('name', $book->getCategory())
            ->firstOrFail();

        DB::transaction(function () use ($book, $workspace, $user, $bookCategoryRepo): void {
            $book = Book::create([
                'workspace_id' => $workspace->getId(),
                'book_category_id' => $bookCategoryRepo->id,
                'status' => $book->getStatus(),
                'title' => $book->getTitle(),
                'description' => $book->getDescription(),
                'url' => $book->getUrl(),
                'image_path' => $book->getImagePath(),
            ]);
            BookHistory::create([
                'book_id' => $book->id,
                'user_id' => $user->getId(),
                'action' => 'create book',
            ]);
        });
    }

    public function update(Entities\Workspace $workspace, Entities\Book $book, Entities\BookHistory|null $bookHistory, Entities\User $user): void
    {
        $bookRepo = $this->findById($book->getId());
        $bookCategoryRepo = BookCategory::where('workspace_id', $workspace->getId())
            ->where('name', $book->getCategory())
            ->firstOrFail();

        DB::transaction(function () use ($book, $bookHistory, $user, $bookRepo, $bookCategoryRepo): void {
            if ($bookHistory) {
                BookHistory::create([
                    'book_id' => $book->getId(),
                    'user_id' => $user->getId(),
                    'action' => $bookHistory->getAction(),
                ]);
            }
            $bookRepo->update([
                'book_category_id' => $bookCategoryRepo->id,
                'status' => $book->getStatus(),
                'title' => $book->getTitle(),
                'description' => $book->getDescription(),
                'image_path' => $book->getImagePath(),
                'url' => $book->getUrl(),
            ]);
        });
    }

    public function findById(int $bookId): Book
    {
        return Book::find($bookId);
    }
}
