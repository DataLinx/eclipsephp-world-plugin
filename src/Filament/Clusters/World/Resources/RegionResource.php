<?php

namespace Eclipse\World\Filament\Clusters\World\Resources;

use Eclipse\Common\Filament\Concerns\HasCachedAbilityChecks;
use Eclipse\World\Filament\Clusters\World;
use Eclipse\World\Filament\Clusters\World\Resources\RegionResource\Pages\ListRegions;
use Eclipse\World\Models\Region;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegionResource extends Resource
{
    use HasCachedAbilityChecks;

    protected static ?string $model = Region::class;

    protected static ?string $slug = 'regions';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?string $cluster = World::class;

    public static function canUpdateAny(): bool
    {
        return static::canOnce('update_region');
    }

    public static function canDeleteAny(): bool
    {
        return static::canOnce('delete_region');
    }

    public static function canRestoreAny(): bool
    {
        return static::canOnce('restore_region');
    }

    public static function canForceDeleteAny(): bool
    {
        return static::canOnce('force_delete_region');
    }

    public static function canBulkDelete(): bool
    {
        return static::canOnce('delete_any_region');
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
                TextInput::make('name')
                    ->label(__('eclipse-world::regions.form.name.label'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('code')
                    ->label(__('eclipse-world::regions.form.code.label'))
                    ->maxLength(10)
                    ->unique(table: Region::class, ignoreRecord: true)
                    ->helperText(__('eclipse-world::regions.form.code.helper')),

                Select::make('parent_id')
                    ->label(__('eclipse-world::regions.form.parent.label'))
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText(__('eclipse-world::regions.form.parent.helper')),

                Checkbox::make('is_special')
                    ->label(__('eclipse-world::regions.form.is_special.label'))
                    ->helperText(__('eclipse-world::regions.form.is_special.helper')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->striped()
            ->columns([
                TextColumn::make('name')
                    ->label(__('eclipse-world::regions.table.name.label'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label(__('eclipse-world::regions.table.code.label'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('parent.name')
                    ->label(__('eclipse-world::regions.table.parent.label'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                IconColumn::make('is_special')
                    ->label(__('eclipse-world::regions.table.is_special.label'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('countries_count')
                    ->label(__('eclipse-world::regions.table.countries_count.label'))
                    ->getStateUsing(function (Region $record): int {
                        return $record->is_special ? $record->special_countries_count : $record->countries_count;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('countries_count', $direction);
                    }),

                TextColumn::make('children_count')
                    ->label(__('eclipse-world::regions.table.children_count.label'))
                    ->counts('children')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_special')
                    ->label(__('eclipse-world::regions.filters.type.label'))
                    ->options([
                        0 => __('eclipse-world::regions.filters.type.geographical'),
                        1 => __('eclipse-world::regions.filters.type.special'),
                    ]),
                SelectFilter::make('parent_id')
                    ->label(__('eclipse-world::regions.filters.parent.label'))
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(__('eclipse-world::regions.actions.edit.label'))
                    ->modalHeading(__('eclipse-world::regions.actions.edit.heading'))
                    ->authorize(fn () => self::canUpdateAny()),

                ActionGroup::make([
                    DeleteAction::make()
                        ->label(__('eclipse-world::regions.actions.delete.label'))
                        ->modalHeading(__('eclipse-world::regions.actions.delete.heading'))
                        ->visible(fn (Region $record) => ! $record->trashed())
                        ->authorize(fn () => self::canDeleteAny()),

                    RestoreAction::make()
                        ->label(__('eclipse-world::regions.actions.restore.label'))
                        ->modalHeading(__('eclipse-world::regions.actions.restore.heading'))
                        ->visible(fn (Region $record) => $record->trashed())
                        ->authorize(fn () => self::canRestoreAny()),

                    ForceDeleteAction::make()
                        ->label(__('eclipse-world::regions.actions.force_delete.label'))
                        ->modalHeading(__('eclipse-world::regions.actions.force_delete.heading'))
                        ->modalDescription(fn (Region $record): string => __('eclipse-world::regions.actions.force_delete.description', [
                            'name' => $record->name,
                        ]))
                        ->visible(fn (Region $record) => $record->trashed())
                        ->authorize(fn () => self::canForceDeleteAny()),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('eclipse-world::regions.actions.delete.label'))
                        ->authorize(fn () => self::canBulkDelete()),

                    RestoreBulkAction::make()
                        ->label(__('eclipse-world::regions.actions.restore.label'))
                        ->authorize(fn () => self::canRestoreAny()),

                    ForceDeleteBulkAction::make()
                        ->label(__('eclipse-world::regions.actions.force_delete.label'))
                        ->authorize(fn () => self::canForceDeleteAny()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegions::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with('parent')
            ->withCount([
                'countries',
                'specialCountries' => function ($query) {
                    $query->where('world_country_in_special_region.start_date', '<=', now())
                        ->where(function ($query) {
                            $query->whereNull('world_country_in_special_region.end_date')
                                ->orWhere('world_country_in_special_region.end_date', '>=', now());
                        });
                },
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('eclipse-world::regions.nav_label');
    }

    public static function getBreadcrumb(): string
    {
        return __('eclipse-world::regions.breadcrumb');
    }

    public static function getPluralModelLabel(): string
    {
        return __('eclipse-world::regions.plural');
    }
}
