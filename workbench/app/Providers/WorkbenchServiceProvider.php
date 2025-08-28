<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(\Spatie\Permission\PermissionServiceProvider::class);
        $this->app->register(\BezhanSalleh\FilamentShield\FilamentShieldServiceProvider::class);
        $this->app->register(\Livewire\LivewireServiceProvider::class);
        $this->app->register(\Filament\FilamentServiceProvider::class);
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
