<?php

namespace Eclipse\World\Filament\Clusters\World\Resources\RegionResource\Pages;

use Eclipse\World\Filament\Clusters\World\Resources\RegionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegions extends ListRecords
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('eclipse-world::regions.actions.create.label'))
                ->modalHeading(__('eclipse-world::regions.actions.create.heading')),
        ];
    }
}
