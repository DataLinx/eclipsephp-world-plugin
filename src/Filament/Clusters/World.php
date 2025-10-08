<?php

namespace Eclipse\World\Filament\Clusters;

use Filament\Clusters\Cluster;

class World extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-s-map';

    public static function getNavigationLabel(): string
    {
        return __('eclipse-world::cluster.label');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return self::getNavigationLabel();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('eclipse-common::nav.configuration');
    }
}
