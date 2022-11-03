<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Domain\Entities;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;

class BookCategoryRepository
{
    public function store(Entities\Workspace $workspace, Entities\BookCategory $bookCategory): void
    {
        BookCategory::create([
            'workspace_id' => $workspace->getId(),
            'name' => $bookCategory->getName(),
        ]);
    }

    public function delete(Entities\Workspace $workspace, Entities\BookCategory $bookCategory): void
    {
        \DB::transaction(function () use ($bookCategory, $workspace): void {
            $bookCategoryRepo = $this->findByWorkspaceIdAndName($workspace->getId(), $bookCategory->getName());
            $bookCategoryRepo->books->each(function ($book) use ($workspace): void {
                $all = BookCategory::where('workspace_id', $workspace->getId())->where('name', 'ALL')->firstOrFail();
                $book->update(['book_category_id' => $all->id]);
            });
            $bookCategoryRepo->delete();
        });
    }

    public function findByWorkspaceIdAndName(int $workspaceId, string $name): BookCategory
    {
        return BookCategory::where('workspace_id', $workspaceId)->where('name', $name)->firstOrFail();
    }
}
