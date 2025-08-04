<?php

namespace Eclipse\World\Filament\Clusters\World\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Eclipse\World\Filament\Clusters\World;
use Eclipse\World\Filament\Clusters\World\Resources\CurrencyResource\Pages;
use Eclipse\World\Models\Currency;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CurrencyResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Currency::class;

    protected static ?string $slug = 'currencies';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $cluster = World::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->required()
                    ->length(3)
                    ->unique(table: Currency::class, ignoreRecord: true)
                    ->label(__('eclipse-world::currencies.form.id.label'))
                    ->helperText(__('eclipse-world::currencies.form.id.helper')),

                TextInput::make('name')
                    ->label(__('eclipse-world::currencies.form.name.label'))
                    ->required(),

                Toggle::make('is_active')
                    ->label(__('eclipse-world::currencies.form.is_active.label'))
                    ->default(true),
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
                    ->label(__('eclipse-world::currencies.table.id.label'))
                    ->searchable()
                    ->sortable()
                    ->width(100),

                TextColumn::make('name')
                    ->label(__('eclipse-world::currencies.table.name.label'))
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label(__('eclipse-world::currencies.table.is_active.label'))
                    ->boolean()
                    ->width(100),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make()
                    ->label(__('eclipse-world::currencies.actions.edit.label'))
                    ->modalHeading(__('eclipse-world::currencies.actions.edit.heading')),
                ActionGroup::make([
                    DeleteAction::make()
                        ->label(__('eclipse-world::currencies.actions.delete.label'))
                        ->modalHeading(__('eclipse-world::currencies.actions.delete.heading')),
                    RestoreAction::make()
                        ->label(__('eclipse-world::currencies.actions.restore.label'))
                        ->modalHeading(__('eclipse-world::currencies.actions.restore.heading')),
                    ForceDeleteAction::make()
                        ->label(__('eclipse-world::currencies.actions.force_delete.label'))
                        ->modalHeading(__('eclipse-world::currencies.actions.force_delete.heading'))
                        ->modalDescription(fn (Currency $record): string => __('eclipse-world::currencies.actions.force_delete.description', [
                            'name' => $record->name,
                        ])),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('eclipse-world::currencies.actions.delete.label')),
                    RestoreBulkAction::make()
                        ->label(__('eclipse-world::currencies.actions.restore.label')),
                    ForceDeleteBulkAction::make()
                        ->label(__('eclipse-world::currencies.actions.force_delete.label')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCurrencies::route('/'),
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
        return __('eclipse-world::currencies.nav_label');
    }

    public static function getBreadcrumb(): string
    {
        return __('eclipse-world::currencies.breadcrumb');
    }

    public static function getPluralModelLabel(): string
    {
        return __('eclipse-world::currencies.plural');
    }
}
