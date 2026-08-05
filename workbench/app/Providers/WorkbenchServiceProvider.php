<?php

namespace Workbench\App\Providers;

use Filament\FilamentServiceProvider;
use Illuminate\Support\ServiceProvider;
use Livewire\LivewireServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->register(LivewireServiceProvider::class);
        $this->app->register(FilamentServiceProvider::class);
        $this->app->register(AdminPanelProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
