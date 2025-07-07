<?php

namespace Eclipse\World;

use Eclipse\World\Console\Commands\ImportCommand;
use Eclipse\World\Console\Commands\ImportPostsCommand;
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
            ])
            ->discoversMigrations()
            ->runsMigrations();
    }
}
