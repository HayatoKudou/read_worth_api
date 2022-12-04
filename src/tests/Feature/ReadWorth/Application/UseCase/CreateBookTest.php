<?php

namespace Tests\Feature\ReadWorth\Application\UseCase;

use Tests\TestCase;
use ReadWorth\Infrastructure\EloquentModel\Role;
use ReadWorth\Infrastructure\EloquentModel\User;
use Illuminate\Auth\Access\AuthorizationException;
use ReadWorth\Application\UseCase\Books\CreateBook;
use ReadWorth\UI\Http\Resources\CreateBookResource;
use ReadWorth\Infrastructure\EloquentModel\Belonging;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Application\UseCase\Books\StoreBookImage;
use ReadWorth\Infrastructure\Repository\BookRepository;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;

class CreateBookTest extends TestCase
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
    public function 書籍登録ができること(): void
    {
        \Auth::setUser($this->canUser);

        $workspaceRepository = \Mockery::mock(WorkspaceRepository::class)
            ->shouldReceive('findById')
            ->once()
            ->andReturn($this->workspace)
            ->getMock();

        $bookRepository = \Mockery::mock(BookRepository::class)
            ->shouldReceive('store')
            ->once()
            ->getMock();

        $storeBookImageMock = \Mockery::mock(StoreBookImage::class);

        $useCase = new CreateBook($workspaceRepository, $bookRepository, $storeBookImageMock);
        $useCase->create(new CreateBookResource(
            workspaceId: $this->workspace->id,
            category: 'マネジメント',
            title: 'すごい本',
            description: 'すごい本',
            image: null,
            url: null,
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

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('ユーザは書籍管理権限がありません');
        $useCase = new CreateBook($workspaceRepository, $bookRepository, $storeBookImageMock);
        $useCase->create(new CreateBookResource(
            workspaceId: $this->workspace->id,
            category: 'マネジメント',
            title: 'すごい本',
            description: 'すごい本',
            image: null,
            url: null,
        ));
    }
}
