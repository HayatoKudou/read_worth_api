<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain\Entities;
use Illuminate\Support\Facades\DB;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\BookHistory;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;
use ReadWorth\Domain\ValueObjects\BookPurchaseApplySteps;
use ReadWorth\Infrastructure\EloquentModel\BookPurchaseApply;

class BookPurchaseApplyRepository
{
    public function store(Entities\Workspace $workspace, Entities\User $user, Entities\Book $book, Entities\BookPurchaseApply $bookPurchaseApply): void
    {
        $bookCategoryRepo = BookCategory::where('workspace_id', $workspace->getId())
            ->where('name', $book->getCategory())
            ->firstOrFail();

        DB::transaction(function () use ($workspace, $user, $book, $bookPurchaseApply, $bookCategoryRepo): void {
            $bookRepo = Book::create([
                'workspace_id' => $workspace->getId(),
                'book_category_id' => $bookCategoryRepo->id,
                'status' => $book->getStatus(),
                'title' => $book->getTitle(),
                'description' => $book->getDescription(),
                'url' => $book->getUrl(),
                'image_path' => $book->getImagePath(),
            ]);
            BookPurchaseApply::create([
                'user_id' => $user->getId(),
                'workspace_id' => $workspace->getId(),
                'book_id' => $bookRepo->id,
                'reason' => $bookPurchaseApply->getReason(),
                'price' => $bookPurchaseApply->getPrice(),
                'step' => $bookPurchaseApply->getStep(),
            ]);
            BookHistory::create([
                'book_id' => $bookRepo->id,
                'user_id' => $user->getId(),
                'action' => 'purchase book',
            ]);
        });
    }

    public function accept(string $bookId, string $userId): void
    {
        DB::transaction(function () use ($bookId, $userId): void {
            Book::find($bookId)->purchaseApply->update([
                'step' => BookPurchaseApplySteps::NEED_BUY,
            ]);
            BookHistory::create([
                'book_id' => $bookId,
                'user_id' => $userId,
                'action' => 'purchase accepted',
            ]);
        });
    }
}
