<?php

namespace Eclipse\World\Filament\Clusters\World\Resources\CountryResource\Pages;

use Eclipse\World\Filament\Clusters\World\Resources\CountryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCountries extends ListRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
