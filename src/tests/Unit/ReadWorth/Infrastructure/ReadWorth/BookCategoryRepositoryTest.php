<?php

namespace Tests\Unit\S4T\Infrastructure\Repository;

use Tests\TestCase;
use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\Entities\BookCategory;
use ReadWorth\Infrastructure\EloquentModel;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Domain\ValueObjects\BookCategoryId;
use ReadWorth\Infrastructure\Repository\BookCategoryRepository;

class BookCategoryRepositoryTest extends TestCase
{
    /** @test */
    public function 書籍カテゴリを登録できること(): void
    {
        $workspace = EloquentModel\Workspace::factory()->create(['name' => 'tete']);
        assert($workspace instanceof EloquentModel\Workspace);

        $bookCategoryId = new BookCategoryId();
        $workspaceDomain = new Workspace(id: $workspace->id, name: 'tete');
        $bookCategoryDomain = new BookCategory(id: $bookCategoryId->getBookCategoryId(), name: 'IT');

        $repository = new BookCategoryRepository();
        $repository->store($workspaceDomain, $bookCategoryDomain);

        $bookCategory = EloquentModel\BookCategory::query()->latest()->first();
        $this->assertSame($workspace->id, $bookCategory->workspace_id, 'book_category.workspace_id');
        $this->assertSame('IT', $bookCategory->name, 'book_category.name');
    }

    /** @test */
    public function 書籍カテゴリを削除できること(): void
    {
        $workspaceId = 1;
        $allBookCategory = EloquentModel\BookCategory::firstOrCreate([
            'workspace_id' => $workspaceId,
            'name' => 'ALL',
        ]);

        $bookCategory = EloquentModel\BookCategory::factory()->create([
            'workspace_id' => $workspaceId,
            'name' => 'aaaaaa',
        ]);

        $book = EloquentModel\Book::factory()->create([
            'workspace_id' => $workspaceId,
            'book_category_id' => $bookCategory->id,
        ]);

        $workspaceDomain = new Workspace(id: $workspaceId, name: 'tete');
        $bookCategoryDomain = new BookCategory(id: $bookCategory->id, name: 'aaaaaa');

        $repository = new BookCategoryRepository();
        $repository->delete($workspaceDomain, $bookCategoryDomain);

        $bookCategory = EloquentModel\BookCategory::where('workspace_id', 1)->where('name', 'aaaaaa')->first();
        $this->assertNull($bookCategory, '書籍カテゴリが削除されていること');
        $book = Book::find($book->id);
        $this->assertSame($allBookCategory->id, $book->book_category_id);
    }
}
