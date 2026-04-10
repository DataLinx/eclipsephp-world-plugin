<?php

namespace Eclipse\World\Filament\Clusters\World\Resources\TariffCodeResource\Pages;

use Eclipse\Common\Helpers\L10nHelper;
use Eclipse\World\Filament\Clusters\World\Resources\TariffCodeResource;
use Eclipse\World\Jobs\ImportTariffCodes;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListTariffCodes extends ListRecords
{
    use Translatable;

    protected static string $resource = TariffCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make()
                ->label(__('eclipse-world::tariff-codes.actions.create.label'))
                ->modalHeading(__('eclipse-world::tariff-codes.actions.create.heading')),
            Action::make('import_tariff_codes')
                ->label(__('eclipse-world::tariff-codes.import.action_label'))
                ->icon('heroicon-o-arrow-down-tray')
                ->schema([
                    Select::make('locales')
                        ->label(__('eclipse-world::tariff-codes.import.locales_label'))
                        ->options(L10nHelper::getLocaleOptions())
                        ->multiple()
                        ->required()
                        ->native(false),
                ])
                ->modalHeading(__('eclipse-world::tariff-codes.import.modal_heading'))
                ->action(function (array $data) {
                    ImportTariffCodes::dispatch(locales: $data['locales']);
                })
                ->requiresConfirmation(),
        ];
    }
}
