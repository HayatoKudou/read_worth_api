<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BookCategoryRepository implements IBookCategoryRepository
{
    public function store(Domain\BookCategory $bookCategory): void
    {
        BookCategory::create([
            'workspace_id' => $bookCategory->getWorkspaceId(),
            'name' => $bookCategory->getName(),
        ]);
    }

    public function delete(Domain\BookCategory $bookCategory): void
    {
        \DB::transaction(function () use ($bookCategory): void {
            $bookCategoryRepo = BookCategory::where('workspace_id', $bookCategory->getWorkspaceId())->where('name', $bookCategory->getName())->firstOrFail();
            $bookCategoryRepo->books->each(function ($book) use ($bookCategory): void {
                $all = BookCategory::where('workspace_id', $bookCategory->getWorkspaceId())->where('name', 'ALL')->firstOrFail();
                \Log::debug($book);

                $book->update(['book_category_id' => $all->id]);
            });
            $bookCategoryRepo->delete();
        });
    }
}
