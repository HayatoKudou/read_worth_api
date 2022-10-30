<?php

namespace Tests\Feature\ReadWorth\Application\UseCase;

use Tests\TestCase;
use ReadWorth\Application\UseCase\CreateBook;
use ReadWorth\Infrastructure\EloquentModel\Role;
use ReadWorth\Infrastructure\EloquentModel\User;
use ReadWorth\Application\UseCase\StoreBookImage;
use ReadWorth\UI\Http\Requests\CreateBookRequest;
use Illuminate\Auth\Access\AuthorizationException;
use ReadWorth\Infrastructure\EloquentModel\Belonging;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
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

        $requestMock = \Mockery::mock(CreateBookRequest::class)
            ->shouldReceive('route')
            ->andReturn($this->workspace->id)
            ->once()
            ->shouldReceive('validated')
            ->andReturn([
                'category' => 'マネジメント',
                'title' => 'すごすご本',
                'description' => 'やばい',
                'image' => '',
                'url' => '',
            ])
            ->once()
            ->getMock();

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
        $useCase->create($requestMock);
    }

    /** @test */
    public function 書籍管理権限がない場合書籍カテゴリの登録ができないこと(): void
    {
        \Auth::setUser($this->canNotUser);

        $requestMock = \Mockery::mock(CreateBookRequest::class)
            ->shouldReceive('route')
            ->andReturn($this->workspace->id)
            ->once()
            ->getMock();

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
        $useCase->create($requestMock);
    }
}
