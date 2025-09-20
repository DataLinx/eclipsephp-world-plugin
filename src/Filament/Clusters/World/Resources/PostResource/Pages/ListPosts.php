<?php

namespace Eclipse\World\Filament\Clusters\World\Resources\PostResource\Pages;

use Eclipse\World\Filament\Clusters\World\Resources\PostResource;
use Eclipse\World\Jobs\ImportPosts;
use Eclipse\World\Models\Country;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
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
                ->schema([
                    Select::make('country_id')
                        ->label(__('eclipse-world::posts.import.country_label'))
                        ->helperText(__('eclipse-world::posts.import.country_helper'))
                        ->options([
                            'SI' => Country::find('SI')?->name ?: 'SI',
                            'HR' => Country::find('HR')?->name ?: 'HR',
                        ])
                        ->required()
                        ->native(false),
                ])
                ->modalHeading(__('eclipse-world::posts.import.modal_heading'))
                ->action(function (array $data) {
                    // Dispatch the job
                    ImportPosts::dispatch(countryId: $data['country_id']);
                })
                ->requiresConfirmation(),
        ];
    }
}
