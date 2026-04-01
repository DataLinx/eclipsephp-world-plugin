<?php

namespace Eclipse\World;

use Eclipse\World\Console\Commands\ImportCommand;
use Eclipse\World\Console\Commands\ImportPostsCommand;
use Eclipse\World\Console\Commands\ImportTariffCodesCommand;
use Eclipse\World\Filament\Clusters\World\Resources\CountryResource;
use Eclipse\World\Filament\Clusters\World\Resources\CurrencyResource;
use Eclipse\World\Filament\Clusters\World\Resources\PostResource;
use Eclipse\World\Filament\Clusters\World\Resources\RegionResource;
use Eclipse\World\Filament\Clusters\World\Resources\TariffCodeResource;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EclipseWorldServiceProvider extends PackageServiceProvider
{
    public static string $name = 'eclipse-world';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasCommands([
                ImportCommand::class,
                ImportPostsCommand::class,
                ImportTariffCodesCommand::class,
            ])
            ->discoversMigrations()
            ->runsMigrations();
    }

    public function boot(): void
    {
        parent::boot();

        // Merge per-resource abilities into the effective config
        $this->app->booted(function () {
            $manage = config('filament-shield.resources.manage', []);

            $pluginManage = [
                CountryResource::class => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'restore',
                    'restoreAny',
                    'delete',
                    'deleteAny',
                    'forceDelete',
                    'forceDeleteAny',
                ],
                RegionResource::class => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'restore',
                    'restoreAny',
                    'delete',
                    'deleteAny',
                    'forceDelete',
                    'forceDeleteAny',
                ],
                CurrencyResource::class => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'restore',
                    'restoreAny',
                    'delete',
                    'deleteAny',
                    'forceDelete',
                    'forceDeleteAny',
                ],
                TariffCodeResource::class => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'restore',
                    'restoreAny',
                    'delete',
                    'deleteAny',
                    'forceDelete',
                    'forceDeleteAny',
                ],
                PostResource::class => [
                    'viewAny',
                    'view',
                    'create',
                    'update',
                    'restore',
                    'restoreAny',
                    'delete',
                    'deleteAny',
                    'forceDelete',
                    'forceDeleteAny',
                ],
            ];

            $manage = array_replace_recursive($manage, $pluginManage);
            config()->set('filament-shield.resources.manage', $manage);
        });
    }
}
