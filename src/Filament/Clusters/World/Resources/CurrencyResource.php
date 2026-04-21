<?php

namespace Eclipse\World\Filament\Clusters\World\Resources;

use Eclipse\Common\Filament\Concerns\HasCachedAbilityChecks;
use Eclipse\World\Filament\Clusters\World;
use Eclipse\World\Filament\Clusters\World\Resources\CurrencyResource\Pages\ListCurrencies;
use Eclipse\World\Models\Currency;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CurrencyResource extends Resource
{
    use HasCachedAbilityChecks;

    protected static ?string $model = Currency::class;

    protected static ?string $slug = 'currencies';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $cluster = World::class;

    public static function canUpdateAny(): bool
    {
        return static::canOnce('update_currency');
    }

    public static function canDeleteAny(): bool
    {
        return static::canOnce('delete_currency');
    }

    public static function canRestoreAny(): bool
    {
        return static::canOnce('restore_currency');
    }

    public static function canForceDeleteAny(): bool
    {
        return static::canOnce('force_delete_currency');
    }

    public static function canBulkDelete(): bool
    {
        return static::canOnce('delete_any_currency');
    }

    public static function canEdit(Model $record): bool
    {
        return static::canUpdateAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canDeleteAny() && ! $record->trashed();
    }

    public static function canRestore(Model $record): bool
    {
        return static::canRestoreAny() && $record->trashed();
    }

    public static function canForceDelete(Model $record): bool
    {
        return static::canForceDeleteAny() && $record->trashed();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
            ->recordActions([
                EditAction::make()
                    ->label(__('eclipse-world::currencies.actions.edit.label'))
                    ->modalHeading(__('eclipse-world::currencies.actions.edit.heading'))
                    ->authorize(fn () => self::canUpdateAny()),

                ActionGroup::make([
                    DeleteAction::make()
                        ->label(__('eclipse-world::currencies.actions.delete.label'))
                        ->modalHeading(__('eclipse-world::currencies.actions.delete.heading'))
                        ->visible(fn (Currency $record) => ! $record->trashed())
                        ->authorize(fn () => self::canDeleteAny()),

                    RestoreAction::make()
                        ->label(__('eclipse-world::currencies.actions.restore.label'))
                        ->modalHeading(__('eclipse-world::currencies.actions.restore.heading'))
                        ->visible(fn (Currency $record) => $record->trashed())
                        ->authorize(fn () => self::canRestoreAny()),

                    ForceDeleteAction::make()
                        ->label(__('eclipse-world::currencies.actions.force_delete.label'))
                        ->modalHeading(__('eclipse-world::currencies.actions.force_delete.heading'))
                        ->modalDescription(fn (Currency $record): string => __('eclipse-world::currencies.actions.force_delete.description', [
                            'name' => $record->name,
                        ]))
                        ->visible(fn (Currency $record) => $record->trashed())
                        ->authorize(fn () => self::canForceDeleteAny()),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('eclipse-world::currencies.actions.delete.label'))
                        ->authorize(fn () => self::canBulkDelete()),

                    RestoreBulkAction::make()
                        ->label(__('eclipse-world::currencies.actions.restore.label'))
                        ->authorize(fn () => self::canRestoreAny()),

                    ForceDeleteBulkAction::make()
                        ->label(__('eclipse-world::currencies.actions.force_delete.label'))
                        ->authorize(fn () => self::canForceDeleteAny()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCurrencies::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
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
