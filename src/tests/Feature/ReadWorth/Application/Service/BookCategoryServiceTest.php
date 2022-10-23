<?php

//
// namespace Tests\Feature\ReadWorth\Application\Service;
//
// use Tests\TestCase;
// use ReadWorth\Domain\BookCategory;
// use ReadWorth\Infrastructure\EloquentModel\Role;
// use ReadWorth\Infrastructure\EloquentModel\User;
// use Illuminate\Auth\Access\AuthorizationException;
// use ReadWorth\Infrastructure\EloquentModel\Belonging;
// use ReadWorth\Infrastructure\EloquentModel\Workspace;
// use ReadWorth\Application\Service\BookCategoryService;
// use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
// use ReadWorth\Infrastructure\Repository\BookCategoryRepository;
//
// class BookCategoryServiceTest extends TestCase
// {
//    private Workspace $workspace;
//    private User $canUser;
//    private User $canNotUser;
//
//    protected function setUp(): void
//    {
//        parent::setUp();
//
//        $this->workspace = Workspace::factory()->create();
//        $this->canUser = User::factory()->create();
//        Belonging::factory()->create([
//            'user_id' => $this->canUser->id,
//            'workspace_id' => $this->workspace->id,
//            'role_id' => Role::factory()->create()->id,
//        ]);
//
//        $this->canNotUser = User::factory()->create();
//        Belonging::factory()->create([
//            'user_id' => $this->canNotUser->id,
//            'workspace_id' => $this->workspace->id,
//            'role_id' => Role::factory()->create([
//                'is_book_manager' => 0,
//            ])->id,
//        ]);
//    }
//
//    public function 書籍カテゴリの登録ができること(): void
//    {
//        \Auth::setUser($this->canUser);
//        $workspaceRepository = \Mockery::mock(WorkspaceRepository::class)
//            ->shouldReceive('findById')
//            ->once()
//            ->andReturn($this->workspace)
//            ->getMock();
//
//        $bookCategoryRepository = \Mockery::mock(BookCategoryRepository::class)
//            ->shouldReceive('store')
//            ->once()
//            ->getMock();
//
//        $bookCategory = new BookCategory(
//            workspaceId: $this->workspace->id,
//            name: 'IT'
//        );
//
//        $service = new BookCategoryService($workspaceRepository, $bookCategoryRepository);
//        $service->create($bookCategory);
//    }
//
//    public function 書籍管理権限がない場合書籍カテゴリの登録ができないこと(): void
//    {
//        \Auth::setUser($this->canNotUser);
//        $workspaceRepository = \Mockery::mock(WorkspaceRepository::class)
//            ->shouldReceive('findById')
//            ->once()
//            ->andReturn($this->workspace)
//            ->getMock();
//
//        $bookCategoryRepository = \Mockery::mock(BookCategoryRepository::class);
//
//        $bookCategory = new BookCategory(
//            workspaceId: $this->workspace->id,
//            name: 'IT'
//        );
//
//        $this->expectException(AuthorizationException::class);
//        $this->expectExceptionMessage('ユーザは書籍管理権限がありません');
//        $service = new BookCategoryService($workspaceRepository, $bookCategoryRepository);
//        $service->create($bookCategory);
//    }
//
//    public function 書籍カテゴリの削除ができること(): void
//    {
//        \Auth::setUser($this->canUser);
//        $workspaceRepository = \Mockery::mock(WorkspaceRepository::class)
//            ->shouldReceive('findById')
//            ->once()
//            ->andReturn($this->workspace)
//            ->getMock();
//
//        $bookCategoryRepository = \Mockery::mock(BookCategoryRepository::class)
//            ->shouldReceive('delete')
//            ->once()
//            ->getMock();
//
//        $bookCategory = new BookCategory(
//            workspaceId: $this->workspace->id,
//            name: 'IT'
//        );
//
//        $service = new BookCategoryService($workspaceRepository, $bookCategoryRepository);
//        $service->delete($bookCategory);
//    }
//
//    public function 書籍管理権限がない場合書籍カテゴリの削除ができないこと(): void
//    {
//        \Auth::setUser($this->canNotUser);
//        $workspaceRepository = \Mockery::mock(WorkspaceRepository::class)
//            ->shouldReceive('findById')
//            ->once()
//            ->andReturn($this->workspace)
//            ->getMock();
//
//        $bookCategoryRepository = \Mockery::mock(BookCategoryRepository::class);
//
//        $bookCategory = new BookCategory(
//            workspaceId: $this->workspace->id,
//            name: 'IT'
//        );
//
//        $this->expectException(AuthorizationException::class);
//        $this->expectExceptionMessage('ユーザは書籍管理権限がありません');
//        $service = new BookCategoryService($workspaceRepository, $bookCategoryRepository);
//        $service->delete($bookCategory);
//    }
// }
