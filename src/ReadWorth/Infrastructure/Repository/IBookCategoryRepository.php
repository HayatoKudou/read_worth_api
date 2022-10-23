<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain\Entities;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

interface IBookCategoryRepository
{
    public function store(Entities\Workspace $workspace, Entities\BookCategory $bookCategory): void;

    public function delete(Entities\Workspace $workspace, Entities\BookCategory $bookCategory): void;

    public function findByWorkspaceIdAndName(int $workspaceId, string $name): BookCategory;
}
