<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

interface IBookCategoryRepository
{
    public function store(Domain\BookCategory $bookCategory): void;

    public function delete(Domain\BookCategory $bookCategory): void;

    public function findByWorkspaceIdAndName(int $workspaceId, string $name): BookCategory;
}
