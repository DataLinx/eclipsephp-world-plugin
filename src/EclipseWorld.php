<?php

namespace Eclipse\World;

use Filament\Contracts\Plugin;
use Filament\Panel;

class EclipseWorld implements Plugin
{
    public function getId(): string
    {
        return 'eclipse-world';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverClusters(__DIR__.'/Filament/Clusters', 'Eclipse\\World\\Filament\\Clusters');
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
