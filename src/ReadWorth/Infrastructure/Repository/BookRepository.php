<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain\Entities;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use ReadWorth\Domain\ValueObjects\BookId;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\BookReview;
use ReadWorth\Infrastructure\EloquentModel\BookHistory;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;
use ReadWorth\Infrastructure\EloquentModel\BookRentalApply;
use ReadWorth\Infrastructure\EloquentModel\BookPurchaseApply;

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

    /**
     * @param Collection<BookId, int> $bookIds
     */
    public function delete(Collection $bookIds): void
    {
        DB::transaction(function () use ($bookIds): void {
            $bookIds->each(function (BookId $bookId): void {
                BookPurchaseApply::where('book_id', $bookId->getBookId())->delete();
                BookRentalApply::where('book_id', $bookId->getBookId())->delete();
                BookReview::where('book_id', $bookId->getBookId())->delete();
                BookHistory::where('book_id', $bookId->getBookId())->delete();
                Book::find($bookId->getBookId())->delete();
            });
        });
    }

    public function findById(int $bookId): Book
    {
        return Book::find($bookId);
    }
}
