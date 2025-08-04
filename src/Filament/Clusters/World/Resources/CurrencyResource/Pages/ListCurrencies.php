<?php

namespace Eclipse\World\Filament\Clusters\World\Resources\CurrencyResource\Pages;

use Eclipse\World\Filament\Clusters\World\Resources\CurrencyResource;
use Eclipse\World\Jobs\ImportCurrencies;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\App;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('eclipse-world::currencies.actions.create.label'))
                ->modalHeading(__('eclipse-world::currencies.actions.create.heading')),
            Action::make('import_currencies')
                ->label(__('eclipse-world::currencies.import.action_label'))
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    // Dispatch the job
                    ImportCurrencies::dispatch(auth()->id(), App::getLocale());

                    // Show notification
                    Notification::make()
                        ->title(__('eclipse-world::currencies.import.success_title'))
                        ->body(__('eclipse-world::currencies.import.success_message'))
                        ->success()
                        ->send();
                })
                ->requiresConfirmation(),
        ];
    }
}
