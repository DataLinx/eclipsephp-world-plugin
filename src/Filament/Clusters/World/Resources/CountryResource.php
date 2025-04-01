<?php

namespace Eclipse\World\Filament\Clusters\World\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Eclipse\World\Filament\Clusters\World;
use Eclipse\World\Filament\Clusters\World\Resources\CountryResource\Pages;
use Eclipse\World\Models\Country;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CountryResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Country::class;

    protected static ?string $slug = 'countries';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = World::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->required()
                    ->length(2)
                    ->unique(table: Country::class, ignoreRecord: true)
                    ->label(__('eclipse-world::countries.form.id.label'))
                    ->helperText(__('eclipse-world::countries.form.id.helper')),

                TextInput::make('name')
                    ->label(__('eclipse-world::countries.form.name.label'))
                    ->required(),

                TextInput::make('flag')
                    ->label(__('eclipse-world::countries.form.flag.label'))
                    ->suffixAction(function () {
                        if (class_exists('\TangoDevIt\FilamentEmojiPicker\EmojiPickerAction')) {
                            return \TangoDevIt\FilamentEmojiPicker\EmojiPickerAction::make('emoji-flag');
                        }

                        return null;
                    }),

                TextInput::make('a3_id')
                    ->required()
                    ->length(3)
                    ->label(__('eclipse-world::countries.form.alpha3_id.label'))
                    ->helperText(__('eclipse-world::countries.form.alpha3_id.helper')),

                TextInput::make('num_code')
                    ->numeric()
                    ->length(3)
                    ->label(__('eclipse-world::countries.form.num_code.label'))
                    ->helperText(__('eclipse-world::countries.form.num_code.helper')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->defaultSort('name')
            ->striped()
            ->columns([
                TextColumn::make('id')
                    ->label(__('eclipse-world::countries.table.id.label'))
                    ->searchable()
                    ->sortable()
                    ->width(100),

                TextColumn::make('name')
                    ->label(__('eclipse-world::countries.table.name.label'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('flag')
                    ->label(__('eclipse-world::countries.table.flag.label'))
                    ->width(100),

                TextColumn::make('a3_id')
                    ->label(__('eclipse-world::countries.table.alpha3_id.label'))
                    ->searchable()
                    ->sortable()
                    ->width(100),

                TextColumn::make('num_code')
                    ->label(__('eclipse-world::countries.table.num_code.label'))
                    ->searchable()
                    ->sortable()
                    ->width(100),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make()
                    ->label(__('eclipse-world::countries.actions.edit.label'))
                    ->modalHeading(__('eclipse-world::countries.actions.edit.heading')),
                ActionGroup::make([
                    DeleteAction::make()
                        ->label(__('eclipse-world::countries.actions.delete.label'))
                        ->modalHeading(__('eclipse-world::countries.actions.delete.heading')),
                    RestoreAction::make()
                        ->label(__('eclipse-world::countries.actions.restore.label'))
                        ->modalHeading(__('eclipse-world::countries.actions.restore.heading')),
                    ForceDeleteAction::make()
                        ->label(__('eclipse-world::countries.actions.force_delete.label'))
                        ->modalHeading(__('eclipse-world::countries.actions.force_delete.heading'))
                        ->modalDescription(fn (Country $record): string => __('eclipse-world::countries.actions.force_delete.description', [
                            'name' => $record->name,
                        ])),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('eclipse-world::countries.actions.delete.label')),
                    RestoreBulkAction::make()
                        ->label(__('eclipse-world::countries.actions.restore.label')),
                    ForceDeleteBulkAction::make()
                        ->label(__('eclipse-world::countries.actions.force_delete.label')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('eclipse-world::countries.nav_label');
    }

    public static function getBreadcrumb(): string
    {
        return __('eclipse-world::countries.breadcrumb');
    }

    public static function getPluralModelLabel(): string
    {
        return __('eclipse-world::countries.plural');
    }
}
