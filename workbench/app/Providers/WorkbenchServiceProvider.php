<?php

namespace App\World\Workbench\Providers;

use Illuminate\Support\ServiceProvider;
use App\World\Workbench\Database\Seeders\DatabaseSeeder;
use App\World\Workbench\Providers\AdminPanelProvider;
use App\World\Workbench\Providers\AuthServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(AdminPanelProvider::class);
        $this->app->register(AuthServiceProvider::class);

        $this->app->bind('DatabaseSeeder', function ($app) {
            return new DatabaseSeeder;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
