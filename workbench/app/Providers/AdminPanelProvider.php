<?php

namespace Workbench\App\Providers;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Eclipse\World\EclipseWorld;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Telescope\Telescope;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;
use Workbench\App\Http\Middleware\WorkbenchBootstrap;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                WorkbenchBootstrap::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                EclipseWorld::make(),
                SpatieTranslatablePlugin::make()
                    ->defaultLocales(['en']),
            ])
            ->viteTheme(false)
            ->pages([
                Dashboard::class,
            ])
            ->navigationItems([
                \Filament\Navigation\NavigationItem::make('Telescope')
                    ->url('/telescope')
                    ->icon('heroicon-o-magnifying-glass')
                    ->group('Development')
                    ->sort(1)
                    ->visible(fn (): bool => $this->app->environment('local', 'testing')),
            ]);
    }

    public function register(): void
    {
        parent::register();

        if ($this->app->environment('local', 'testing')) {
            Telescope::night();
        }
    }
}
