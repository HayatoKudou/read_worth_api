<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain\Entities;
use Illuminate\Support\Facades\DB;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\BookHistory;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;
use ReadWorth\Infrastructure\EloquentModel\SlackCredential;
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

    public function accept(Entities\Book $book, Entities\User $user, Entities\BookPurchaseApply $bookPurchaseApply): void
    {
        DB::transaction(function () use ($book, $user, $bookPurchaseApply): void {
            Book::find($book->getId())->purchaseApply->update([
                'step' => $bookPurchaseApply->getStep(),
            ]);
            BookHistory::create([
                'book_id' => $book->getId(),
                'user_id' => $user->getId(),
                'action' => 'purchase accepted',
            ]);
        });
    }

    public function done(Entities\Book $book, Entities\User $user, Entities\BookPurchaseApply $bookPurchaseApply): void
    {
        DB::transaction(function () use ($book, $user, $bookPurchaseApply): void {
            $bookRepo = Book::find($book->getId());
            $bookRepo->update(['status' => $book->getStatus()]);
            $bookRepo->purchaseApply->update([
                'step' => $bookPurchaseApply->getStep(),
                'location' => $bookPurchaseApply->getLocation(),
            ]);
            BookHistory::create([
                'book_id' => $book->getId(),
                'user_id' => $user->getId(),
                'action' => 'purchase done',
            ]);
        });
    }

    public function refuse(Entities\Book $book, Entities\User $user, Entities\BookPurchaseApply $bookPurchaseApply): void
    {
        DB::transaction(function () use ($book, $user, $bookPurchaseApply): void {
            Book::find($book->getId())->purchaseApply->update([
                'step' => $bookPurchaseApply->getStep(),
            ]);
            BookHistory::create([
                'book_id' => $book->getId(),
                'user_id' => $user->getId(),
                'action' => 'purchase refused',
            ]);
        });
    }

    public function init(Entities\Book $book, Entities\User $user, Entities\BookPurchaseApply $bookPurchaseApply): void
    {
        DB::transaction(function () use ($book, $user, $bookPurchaseApply): void {
            Book::find($book->getId())->purchaseApply->update([
                'step' => $bookPurchaseApply->getStep(),
            ]);
            BookHistory::create([
                'book_id' => $book->getId(),
                'user_id' => $user->getId(),
                'action' => 'purchase init',
            ]);
        });
    }

    public function notification(string $bookId): void
    {
        $book = Book::find($bookId);
        $book->purchaseApply->delete();
    }

    public function findSlackCredentialByWorkspaceId(string $workspaceId): SlackCredential
    {
        return SlackCredential::where('workspace_id', $workspaceId)->firstOrFail();
    }
}
