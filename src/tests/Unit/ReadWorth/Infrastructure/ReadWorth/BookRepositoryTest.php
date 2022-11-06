<?php

namespace Tests\Unit\S4T\Infrastructure\Repository;

use Tests\TestCase;
use ReadWorth\Domain\Entities\Book;
use ReadWorth\Domain\Entities\User;
use ReadWorth\Domain\Entities\Workspace;
use ReadWorth\Domain\ValueObjects\BookId;
use ReadWorth\Domain\Entities\BookHistory;
use ReadWorth\Infrastructure\EloquentModel;
use ReadWorth\Domain\ValueObjects\BookStatus;
use ReadWorth\Infrastructure\Repository\BookRepository;

class BookRepositoryTest extends TestCase
{
    private EloquentModel\Workspace $workspace;
    private EloquentModel\BookCategory $bookCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = EloquentModel\Workspace::factory()->create(['name' => 'tete']);
        $this->bookCategory = EloquentModel\BookCategory::factory()->create([
            'workspace_id' => $this->workspace->id,
            'name' => 'IT',
        ]);
    }

    /** @test */
    public function 書籍を登録できること(): void
    {
        $bookId = new BookId();
        $workspaceDomain = new Workspace(id: $this->workspace->id, name: 'tete');
        $bookDomain = new Book(
            id: $bookId->getBookId(),
            category: $this->bookCategory->name,
            status: BookStatus::STATUS_CAN_LEND,
            title: 'ジョブズの秘密',
            description: '',
            imagePath: null,
            url: null
        );
        $userDomain = new User(id: 1, name: 'Hayato', email: 'kudoh115@gmail.com');

        $repository = new BookRepository();
        $repository->store($workspaceDomain, $bookDomain, $userDomain);

        $book = EloquentModel\Book::query()->latest()->first();
        $bookHistory = EloquentModel\BookHistory::query()->latest()->first();
        $this->assertSame('ジョブズの秘密', $book->title, 'book.title');
        $this->assertSame($book->id, $bookHistory->book_id, 'book_histories.book_id');
    }

    /** @test */
    public function 書籍を更新できること(): void
    {
        $book = EloquentModel\Book::factory()->create(['book_category_id' => $this->bookCategory->id]);
        assert($book instanceof EloquentModel\Book);

        $workspaceDomain = new Workspace(id: $this->workspace->id, name: 'tete');
        $bookDomain = new Book(
            id: $book->id,
            category: $this->bookCategory->name,
            status: $book->status,
            title: 'ジョブズの秘密',
            description: $book->description,
            imagePath: $book->image_path,
            url: $book->url
        );
        $bookHistoryDomain = new BookHistory(action: 'return book');
        $userDomain = new User(id: 1, name: 'Hayato', email: 'kudoh115@gmail.com');

        $repository = new BookRepository();
        $repository->update($workspaceDomain, $bookDomain, $bookHistoryDomain, $userDomain);

        $latestBook = EloquentModel\Book::query()->latest()->first();
        $latestBookHistory = EloquentModel\BookHistory::query()->latest()->first();
        $this->assertSame('ジョブズの秘密', $latestBook->title, 'book.title');
        $this->assertSame('return book', $latestBookHistory->action, 'book_history.action');
    }

    /** @test */
    public function 書籍を削除できること(): void
    {
        $book = EloquentModel\Book::factory()->create();
        assert($book instanceof EloquentModel\Book);

        $repository = new BookRepository();
        $repository->delete(collect([new BookId($book->id)]));

        $bookExists = EloquentModel\Book::where('id', $book->id)->exists();
        $this->assertFalse($bookExists);
    }

    /** @test */
    public function 書籍を返却できること(): void
    {
        $book = EloquentModel\Book::factory()->create(['status' => BookStatus::STATUS_CAN_NOT_LEND]);
        EloquentModel\BookRentalApply::factory()->create(['book_id' => $book->id]);

        $bookIdDomain = new BookId($book->id);
        $userDomain = new User(id: 1, name: 'Hayato', email: 'kudoh115@gmail.com');

        $repository = new BookRepository();
        $repository->return($bookIdDomain, $userDomain);

        $latestBook = EloquentModel\Book::query()->latest()->first();
        $latestBookRentalApply = EloquentModel\BookRentalApply::query()->latest()->first();

        $this->assertSame(BookStatus::STATUS_CAN_LEND, $latestBook->status);
        $this->assertNotNull($latestBookRentalApply->return_date);
    }
}
