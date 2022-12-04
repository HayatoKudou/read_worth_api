<?php

namespace Tests\Feature\ReadWorth\Application\UseCase;

use Tests\TestCase;
use ReadWorth\Infrastructure\EloquentModel\Role;
use ReadWorth\Infrastructure\EloquentModel\User;
use Illuminate\Auth\Access\AuthorizationException;
use ReadWorth\Infrastructure\EloquentModel\Belonging;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Infrastructure\EloquentModel\BookCategory;
use ReadWorth\UI\Http\Resources\CreateBookCategoryResource;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\Infrastructure\Repository\BookCategoryRepository;
use ReadWorth\Application\UseCase\BookCategories\CreateBookCategory;

class CreateBookCategoryTest extends TestCase
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
            'role_id' => Role::factory()->create(['is_book_manager' => 0])->id,
        ]);
    }

    /** @test */
    public function 書籍カテゴリ登録ができること(): void
    {
        \Auth::setUser($this->canUser);

        $workspaceRepository = \Mockery::mock(WorkspaceRepository::class)
            ->shouldReceive('findById')
            ->once()
            ->andReturn($this->workspace)
            ->getMock();

        $bookCategoryRepository = \Mockery::mock(BookCategoryRepository::class)
            ->shouldReceive('store')
            ->once()
            ->getMock();

        $useCase = new CreateBookCategory($workspaceRepository, $bookCategoryRepository);
        $useCase->create(new CreateBookCategoryResource(
            workspaceId: $this->workspace->id,
            name: 'マネジメント',
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

        $bookCategoryRepository = \Mockery::mock(BookCategoryRepository::class);

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('ユーザは書籍管理権限がありません');
        $useCase = new CreateBookCategory($workspaceRepository, $bookCategoryRepository);
        $useCase->create(new CreateBookCategoryResource(
            workspaceId: $this->workspace->id,
            name: 'マネジメント',
        ));
    }
}
