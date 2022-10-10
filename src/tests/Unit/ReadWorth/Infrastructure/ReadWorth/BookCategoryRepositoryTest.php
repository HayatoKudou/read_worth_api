<?php

namespace Tests\Unit\S4T\Infrastructure\Repository;

use ReadWorth\Infrastructure\Repository\BookCategoryRepository;
use ReadWorth\Infrastructure\EloquentModel;
use Tests\TestCase;
use ReadWorth\Domain\BookCategory;

class BookCategoryRepositoryTest extends TestCase
{
    /** @test */
    public function 連携Twitterアカウントを保存できること(): void
    {
        $bookCategoryDomain = new BookCategory(
            workspaceId: 1,
            name: "IT"
        );

        $repository = new BookCategoryRepository();
        $repository->store($bookCategoryDomain);

        $bookCategory = EloquentModel\BookCategory::query()->latest()->first();
        $this->assertSame(1, $bookCategory->workspace_id, "book_category.workspace_id");
        $this->assertSame('IT', $bookCategory->name, "book_category.name");
    }
}
