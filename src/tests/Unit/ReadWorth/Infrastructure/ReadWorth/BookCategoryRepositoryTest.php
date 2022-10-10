<?php

namespace Tests\Unit\S4T\Infrastructure\Repository;

use Tests\TestCase;
use ReadWorth\Domain\BookCategory;
use ReadWorth\Infrastructure\EloquentModel;
use ReadWorth\Infrastructure\Repository\BookCategoryRepository;

class BookCategoryRepositoryTest extends TestCase
{
    /** @test */
    public function 書籍カテゴリを登録できること(): void
    {
        $bookCategoryDomain = new BookCategory(
            workspaceId: 1,
            name: 'IT'
        );

        $repository = new BookCategoryRepository();
        $repository->store($bookCategoryDomain);

        $bookCategory = EloquentModel\BookCategory::query()->latest()->first();
        $this->assertSame(1, $bookCategory->workspace_id, 'book_category.workspace_id');
        $this->assertSame('IT', $bookCategory->name, 'book_category.name');
    }

    /** @test */
    public function 書籍カテゴリを削除できること(): void
    {
        EloquentModel\BookCategory::factory()->create([
            'workspace_id' => 1,
            'name' => 'aaaaaa',
        ]);

        $bookCategoryDomain = new BookCategory(
            workspaceId: 1,
            name: 'aaaaaa'
        );

        $repository = new BookCategoryRepository();
        $repository->delete($bookCategoryDomain);

        $bookCategory = EloquentModel\BookCategory::where('workspace_id', 1)->where('name', 'aaaaaa')->first();
        $this->assertNull($bookCategory);

        // TODO: カテゴリがALLになること
    }
}
