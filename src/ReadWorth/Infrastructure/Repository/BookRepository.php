<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\BookHistory;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BookRepository implements IBookRepository
{
    public function store(Domain\Book $book, Domain\BookCategory $bookCategory): void
    {
        DB::transaction(function () use ($book, $bookCategory): void {
            $bookCategory = BookCategory::where('workspace_id', $bookCategory->getWorkspaceId())
                ->where('name', $bookCategory->getName())
                ->firstOrFail();

            $book = Book::create([
                'workspace_id' => $book->getWorkspaceId(),
                'book_category_id' => $bookCategory->id,
                'status' => $book->getStatus(),
                'title' => $book->getTitle(),
                'description' => $book->getDescription(),
                'url' => $book->getUrl(),
                'image_path' => $book->getImagePath(),
            ]);
            BookHistory::create([
                'book_id' => $book->id,
                'user_id' => Auth::id(),
                'action' => 'create book',
            ]);
        });
    }
}
