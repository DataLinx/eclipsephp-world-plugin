<?php

namespace Eclipse\World\Filament\Clusters\World\Resources;

use Closure;
use Eclipse\Common\Filament\Concerns\HasCachedAbilityChecks;
use Eclipse\World\Filament\Clusters\World;
use Eclipse\World\Filament\Clusters\World\Resources\CountryResource\Pages\ListCountries;
use Eclipse\World\Models\Country;
use Eclipse\World\Models\CountryInSpecialRegion;
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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use TangoDevIt\FilamentEmojiPicker\EmojiPickerAction;

class CountryResource extends Resource
{
    use HasCachedAbilityChecks;

    protected static ?string $model = Country::class;

    protected static ?string $slug = 'countries';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = World::class;

    public static function canUpdateAny(): bool
    {
        return static::canOnce('update_country');
    }

    public static function canDeleteAny(): bool
    {
        return static::canOnce('delete_country');
    }

    public static function canRestoreAny(): bool
    {
        return static::canOnce('restore_country');
    }

    public static function canForceDeleteAny(): bool
    {
        return static::canOnce('force_delete_country');
    }

    public static function canBulkDelete(): bool
    {
        return static::canOnce('delete_any_country');
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
                            return EmojiPickerAction::make('emoji-flag');
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

                Select::make('region_id')
                    ->label(__('eclipse-world::countries.form.region.label'))
                    ->relationship('region', 'name', fn ($query) => $query->where('is_special', false))
                    ->searchable()
                    ->preload()
                    ->helperText(__('eclipse-world::countries.form.region.helper')),

                Repeater::make('countryInSpecialRegions')
                    ->relationship()
                    ->label(__('eclipse-world::countries.form.special_regions.label'))
                    ->columns(3)
                    ->columnSpan(2)
                    ->createItemButtonLabel(__('eclipse-world::countries.form.special_regions.add_button'))
                    ->defaultItems(0)
                    ->minItems(0)
                    ->schema([
                        Select::make('region_id')
                            ->relationship('region', 'name', fn ($query) => $query->where('is_special', true))
                            ->searchable()
                            ->preload()
                            ->label(__('eclipse-world::countries.form.special_regions.region_label'))
                            ->rules([
                                function ($get) {
                                    return function (string $attribute, $value, Closure $fail) use ($get) {
                                        if (! $value) {
                                            return;
                                        }

                                        $countryId = $get('../../id');
                                        $currentRecordId = $get('id');

                                        if (! $countryId) {
                                            return;
                                        }

                                        // Check for any existing membership with same country and region
                                        $query = CountryInSpecialRegion::where('country_id', $countryId)
                                            ->where('region_id', $value);

                                        // Exclude current record when editing
                                        if ($currentRecordId) {
                                            $query->where('id', '!=', $currentRecordId);
                                        }

                                        if ($query->exists()) {
                                            $regionName = Region::find($value)?->name ?? __('eclipse-world::countries.validation.unknown_region');
                                            $fail(__('eclipse-world::countries.validation.duplicate_special_region_membership', [
                                                'region' => $regionName,
                                            ]));
                                        }
                                    };
                                },
                            ]),

                        DatePicker::make('start_date')
                            ->required()
                            ->label(__('eclipse-world::countries.form.special_regions.start_date_label')),

                        DatePicker::make('end_date')
                            ->nullable()
                            ->label(__('eclipse-world::countries.form.special_regions.end_date_label')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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

                TextColumn::make('region.name')
                    ->label(__('eclipse-world::countries.table.region.label'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('special_regions')
                    ->label(__('eclipse-world::countries.table.special_regions.label'))
                    ->getStateUsing(fn ($record) => $record->specialRegions->pluck('name')->join(', '))
                    ->placeholder('—')
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('region_id')
                    ->label(__('eclipse-world::countries.filters.geographical_region.label'))
                    ->relationship('region', 'name', fn ($query) => $query->where('is_special', false))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('special_regions')
                    ->label(__('eclipse-world::countries.filters.special_region.label'))
                    ->options(function () {
                        return Region::where('is_special', true)
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if (! empty($data['value'])) {
                            return $query->whereHas('specialRegions', function (Builder $query) use ($data) {
                                $query->where('world_regions.id', $data['value'])
                                    ->where('world_country_in_special_region.start_date', '<=', now())
                                    ->where(function ($query) {
                                        $query->whereNull('world_country_in_special_region.end_date')
                                            ->orWhere('world_country_in_special_region.end_date', '>=', now());
                                    });
                            });
                        }

                        return $query;
                    })
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(__('eclipse-world::countries.actions.edit.label'))
                    ->modalHeading(__('eclipse-world::countries.actions.edit.heading'))
                    ->authorize(fn () => self::canUpdateAny()),

                ActionGroup::make([
                    DeleteAction::make()
                        ->label(__('eclipse-world::countries.actions.delete.label'))
                        ->modalHeading(__('eclipse-world::countries.actions.delete.heading'))
                        ->visible(fn (Country $record) => ! $record->trashed())
                        ->authorize(fn () => self::canDeleteAny()),

                    RestoreAction::make()
                        ->label(__('eclipse-world::countries.actions.restore.label'))
                        ->modalHeading(__('eclipse-world::countries.actions.restore.heading'))
                        ->visible(fn (Country $record) => $record->trashed())
                        ->authorize(fn () => self::canRestoreAny()),

                    ForceDeleteAction::make()
                        ->label(__('eclipse-world::countries.actions.force_delete.label'))
                        ->modalHeading(__('eclipse-world::countries.actions.force_delete.heading'))
                        ->modalDescription(fn (Country $record): string => __('eclipse-world::countries.actions.force_delete.description', [
                            'name' => $record->name,
                        ]))
                        ->visible(fn (Country $record) => $record->trashed())
                        ->authorize(fn () => self::canForceDeleteAny()),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('eclipse-world::countries.actions.delete.label'))
                        ->authorize(fn () => self::canBulkDelete()),

                    RestoreBulkAction::make()
                        ->label(__('eclipse-world::countries.actions.restore.label'))
                        ->authorize(fn () => self::canRestoreAny()),

                    ForceDeleteBulkAction::make()
                        ->label(__('eclipse-world::countries.actions.force_delete.label'))
                        ->authorize(fn () => self::canForceDeleteAny()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCountries::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with([
                'region',
                'specialRegions' => function ($query) {
                    $query->wherePivot('start_date', '<=', now())
                        ->where(function ($query) {
                            $query->whereNull('world_country_in_special_region.end_date')
                                ->orWhere('world_country_in_special_region.end_date', '>=', now());
                        });
                },
            ]);
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
