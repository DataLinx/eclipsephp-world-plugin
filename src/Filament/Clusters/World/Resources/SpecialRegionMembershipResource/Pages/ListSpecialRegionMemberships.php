<?php

namespace Eclipse\World\Filament\Clusters\World\Resources\SpecialRegionMembershipResource\Pages;

use Eclipse\World\Filament\Clusters\World\Resources\SpecialRegionMembershipResource;
use Eclipse\World\Models\CountrySpecialRegion;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpecialRegionMemberships extends ListRecords
{
    protected static string $resource = SpecialRegionMembershipResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('eclipse-world::special-memberships.actions.create.label'))
                ->modalHeading(__('eclipse-world::special-memberships.actions.create.heading'))
                ->using(function (array $data) {
                    return CountrySpecialRegion::create($data);
                })
                ->successNotificationTitle(__('eclipse-world::special-memberships.actions.create.success')),
        ];
    }
}
