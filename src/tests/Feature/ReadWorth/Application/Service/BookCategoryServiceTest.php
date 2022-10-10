<?php

namespace Tests\Feature\ReadWorth\Application\Service;

use Tests\TestCase;
use ReadWorth\Domain\BookCategory;
use ReadWorth\Infrastructure\EloquentModel\Role;
use ReadWorth\Infrastructure\EloquentModel\User;
use ReadWorth\Infrastructure\EloquentModel\Belonging;
use ReadWorth\Infrastructure\EloquentModel\Workspace;
use ReadWorth\Application\Service\BookCategoryService;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\Infrastructure\Repository\BookCategoryRepository;

class BookCategoryServiceTest extends TestCase
{
    private Workspace $workspace;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::factory()->create();
        $this->user = User::factory()->create();
        Belonging::factory()->create([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
            'role_id' => Role::factory()->create()->id,
        ]);
    }

    /** @test */
    public function 書籍カテゴリの登録ができること(): void
    {
        \Auth::setUser($this->user);
        $workspaceRepository = \Mockery::mock(WorkspaceRepository::class)
            ->shouldReceive('findById')
            ->once()
            ->andReturn($this->workspace)
            ->getMock();

        $bookCategoryRepository = \Mockery::mock(BookCategoryRepository::class)
            ->shouldReceive('store')
            ->once()
            ->getMock();

        $bookCategory = new BookCategory(
            workspaceId: $this->workspace->id,
            name: 'IT'
        );

        $service = new BookCategoryService($workspaceRepository, $bookCategoryRepository);
        $service->create($bookCategory);
    }
}
