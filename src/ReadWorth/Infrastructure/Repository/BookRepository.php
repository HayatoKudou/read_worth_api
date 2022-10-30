<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain\Entities;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use ReadWorth\Domain\IBookRepository;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookHistory;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BookRepository implements IBookRepository
{
    public function store(Entities\Workspace $workspace, Entities\Book $book, Entities\BookCategory $bookCategory): void
    {
        $workspaceRepo = Workspace::where('name', $workspace->getName())->firstOrFail();
        $bookCategoryRepo = BookCategory::where('workspace_id', $workspaceRepo->id)
            ->where('name', $bookCategory->getName())
            ->first();

        DB::transaction(function () use ($book, $bookCategoryRepo, $workspaceRepo): void {
            $book = Book::create([
                'workspace_id' => $workspaceRepo->id,
                'book_category_id' => $bookCategoryRepo->id,
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

    public function update(Entities\Workspace $workspace, Entities\Book $book, Entities\BookCategory $bookCategory, Entities\BookHistory|null $bookHistory): void
    {
        $bookRepo = $this->findById($book->getId());
        $bookCategoryRepo = BookCategory::where('workspace_id', $workspace->getId())
            ->where('name', $bookCategory->getName())
            ->firstOrFail();

        DB::transaction(function () use ($book, $bookRepo, $bookHistory, $workspace, $bookCategoryRepo): void {
            if ($bookHistory) {
                BookHistory::create([
                    'book_id' => $book->getId(),
                    'user_id' => Auth::id(),
                    'action' => $bookHistory->getAction(),
                ]);
            }
            $bookRepo->update([
                'workspace_id' => $workspace->getId(),
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
