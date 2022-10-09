<?php

namespace App\Providers;

use ReadWorth\Infrastructure\Repository\IUserRepository;
use Illuminate\Support\ServiceProvider;
use ReadWorth\Infrastructure\Repository\IConnectRepository;
use ReadWorth\Infrastructure\Repository\UserRepository;
use ReadWorth\Infrastructure\Repository\ConnectRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            IConnectRepository::class,
            ConnectRepository::class,
        );

        $this->app->bind(
            IUserRepository::class,
            UserRepository::class,
        );
    }

    public function boot(): void
    {
    }
}
