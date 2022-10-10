<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BookCategoryRepository implements IBookCategoryRepository
{
    public function store(\ReadWorth\Domain\BookCategory $bookCategory): void
    {
        BookCategory::create([
            'workspace_id' => $bookCategory->getWorkspaceId(),
            'name' => $bookCategory->getName(),
        ]);
    }
}
