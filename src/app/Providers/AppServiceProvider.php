<?php

namespace App\Providers;

use ReadWorth\Domain\IBookRepository;
use ReadWorth\Domain\IUserRepository;
use Illuminate\Support\ServiceProvider;
use ReadWorth\Domain\IConnectRepository;
use ReadWorth\Domain\IWorkspaceRepository;
use ReadWorth\Domain\IBookCategoryRepository;
use ReadWorth\Infrastructure\Repository\BookRepository;
use ReadWorth\Infrastructure\Repository\UserRepository;
use ReadWorth\Infrastructure\Repository\ConnectRepository;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\Infrastructure\Repository\BookCategoryRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IConnectRepository::class, ConnectRepository::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IBookRepository::class, BookRepository::class);
        $this->app->bind(IBookCategoryRepository::class, BookCategoryRepository::class);
        $this->app->bind(IWorkspaceRepository::class, WorkspaceRepository::class);
    }

    public function boot(): void
    {
    }
}
