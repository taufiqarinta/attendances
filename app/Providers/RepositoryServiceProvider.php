<?php

namespace App\Providers;

use App\Repositories\AdminRepositories;
use App\Repositories\Interfaces\AdminRepositoryInterfaces;
use App\Repositories\Interfaces\UserRepositoryInterfaces;
use App\Repositories\UserRepositories;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            AdminRepositoryInterfaces::class,
            AdminRepositories::class,
        );

        $this->app->bind(
            UserRepositoryInterfaces::class,
            UserRepositories::class,
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
