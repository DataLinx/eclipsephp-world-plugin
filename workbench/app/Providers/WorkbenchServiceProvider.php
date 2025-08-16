<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use Workbench\Database\Seeders\DatabaseSeeder;

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
