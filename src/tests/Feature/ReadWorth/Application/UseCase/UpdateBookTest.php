<?php

namespace Tests\Feature\ReadWorth\Application\UseCase;

use Tests\TestCase;
use ReadWorth\Domain\Services\BookService;
use ReadWorth\Application\UseCase\UpdateBook;
use ReadWorth\Domain\ValueObjects\BookStatus;
use ReadWorth\Infrastructure\EloquentModel\Book;
use ReadWorth\Infrastructure\EloquentModel\Role;
use ReadWorth\Infrastructure\EloquentModel\User;
use ReadWorth\Application\UseCase\StoreBookImage;
use Illuminate\Auth\Access\AuthorizationException;
use ReadWorth\Application\UseCase\DeleteBookImage;
use ReadWorth\UI\Http\Resources\UpdateBookResource;
use ReadWorth\Infrastructure\EloquentModel\Belonging;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\Repository\BookRepository;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;

class UpdateBookTest extends TestCase
{
    private Workspace $workspace;
    private User $canUser;
    private User $canNotUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::factory()->create();
        BookCategory::factory()->create([
            'workspace_id' => $this->workspace->id,
            'name' => 'マネジメント',
        ]);

        $this->canUser = User::factory()->create();
        Belonging::factory()->create([
            'user_id' => $this->canUser->id,
            'workspace_id' => $this->workspace->id,
            'role_id' => Role::factory()->create()->id,
        ]);

        $this->canNotUser = User::factory()->create();
        Belonging::factory()->create([
            'user_id' => $this->canNotUser->id,
            'workspace_id' => $this->workspace->id,
            'role_id' => Role::factory()->create([
                'is_book_manager' => 0,
            ])->id,
        ]);
    }

    /** @test */
    public function 書籍更新ができること(): void
    {
        \Auth::setUser($this->canUser);

        $book = Book::factory()->create(['workspace_id' => $this->workspace->id, 'title' => 'こけこっこ']);
        assert($book instanceof Book);

        $workspaceRepository = \Mockery::mock(WorkspaceRepository::class)
            ->shouldReceive('findById')
            ->once()
            ->andReturn($this->workspace)
            ->getMock();

        $bookRepository = \Mockery::mock(BookRepository::class)
            ->shouldReceive('update')
            ->once()
            ->getMock();

        $storeBookImageMock = \Mockery::mock(StoreBookImage::class);
        $deleteBookImageMock = \Mockery::mock(DeleteBookImage::class)
            ->shouldReceive('delete')
            ->once()
            ->getMock();
        $bookServiceMock = \Mockery::mock(BookService::class)
            ->shouldReceive('updateAction')
            ->once()
            ->getMock();

        $useCase = new UpdateBook($workspaceRepository, $bookRepository, $storeBookImageMock, $deleteBookImageMock, $bookServiceMock);
        $useCase->update(new UpdateBookResource(
            id: $book->id,
            workspaceId: $this->workspace->id,
            category: 'マネジメント',
            status: BookStatus::STATUS_CAN_NOT_LEND,
            title: 'すごすご本',
            description: 'やばい',
            image: '',
            url: '',
        ));
    }

    /** @test */
    public function 書籍管理権限がない場合書籍カテゴリの登録ができないこと(): void
    {
        \Auth::setUser($this->canNotUser);

        $workspaceRepository = \Mockery::mock(WorkspaceRepository::class)
            ->shouldReceive('findById')
            ->once()
            ->andReturn($this->workspace)
            ->getMock();

        $bookRepository = \Mockery::mock(BookRepository::class);
        $storeBookImageMock = \Mockery::mock(StoreBookImage::class);
        $deleteBookImageMock = \Mockery::mock(DeleteBookImage::class);
        $bookServiceMock = \Mockery::mock(BookService::class);

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('ユーザは書籍管理権限がありません');

        $useCase = new UpdateBook($workspaceRepository, $bookRepository, $storeBookImageMock, $deleteBookImageMock, $bookServiceMock);
        $useCase->update(new UpdateBookResource(
            id: 1, // ダミー
            workspaceId: $this->workspace->id,
            category: 'マネジメント',
            status: BookStatus::STATUS_CAN_NOT_LEND,
            title: 'すごすご本',
            description: 'やばい',
            image: '',
            url: '',
        ));
    }
}
