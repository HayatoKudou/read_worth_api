<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ReadWorth\Infrastructure\Repository\UserRepository;
use ReadWorth\Infrastructure\Repository\IUserRepository;
use ReadWorth\Infrastructure\Repository\ConnectRepository;
use ReadWorth\Infrastructure\Repository\IConnectRepository;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;
use ReadWorth\Infrastructure\Repository\IWorkspaceRepository;
use ReadWorth\Infrastructure\Repository\BookCategoryRepository;
use ReadWorth\Infrastructure\Repository\IBookCategoryRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IConnectRepository::class, ConnectRepository::class);
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IBookCategoryRepository::class, BookCategoryRepository::class);
        $this->app->bind(IWorkspaceRepository::class, WorkspaceRepository::class);
    }

    public function boot(): void
    {
    }
}
