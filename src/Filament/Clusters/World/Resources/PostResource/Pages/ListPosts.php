<?php

namespace Eclipse\World\Filament\Clusters\World\Resources\PostResource\Pages;

use Eclipse\World\Filament\Clusters\World\Resources\PostResource;
use Eclipse\World\Jobs\ImportPosts;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('eclipse-world::posts.actions.create.label'))
                ->modalHeading(__('eclipse-world::posts.actions.create.heading')),
            Action::make('import_posts')
                ->label(__('eclipse-world::posts.import.action_label'))
                ->icon('heroicon-o-arrow-down-tray')
                ->form([
                    Select::make('country_id')
                        ->label(__('eclipse-world::posts.import.country_label'))
                        ->helperText(__('eclipse-world::posts.import.country_helper'))
                        ->options([
                            'SI' => __('eclipse-world::posts.import.countries.SI'),
                            'HR' => __('eclipse-world::posts.import.countries.HR'),
                        ])
                        ->required()
                        ->native(false),
                ])
                ->modalHeading(__('eclipse-world::posts.import.modal_heading'))
                ->action(function (array $data) {
                    // Dispatch the job with selected country
                    ImportPosts::dispatch($data['country_id']);

                    // Show notification
                    Notification::make()
                        ->title(__('eclipse-world::posts.import.success_title'))
                        ->body(__('eclipse-world::posts.import.success_message', [
                            'country' => __('eclipse-world::posts.import.countries.'.$data['country_id']),
                        ]))
                        ->success()
                        ->send();
                })
                ->requiresConfirmation(),
        ];
    }
}
