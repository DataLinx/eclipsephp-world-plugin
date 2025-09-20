<?php

namespace Workbench\App\Providers;

use BezhanSalleh\FilamentShield\FilamentShieldServiceProvider;
use Filament\FilamentServiceProvider;
use Illuminate\Support\ServiceProvider;
use Livewire\LivewireServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(PermissionServiceProvider::class);
        $this->app->register(FilamentShieldServiceProvider::class);
        $this->app->register(LivewireServiceProvider::class);
        $this->app->register(FilamentServiceProvider::class);
        $this->app->register(AdminPanelProvider::class);
        $this->app->register(AuthServiceProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
