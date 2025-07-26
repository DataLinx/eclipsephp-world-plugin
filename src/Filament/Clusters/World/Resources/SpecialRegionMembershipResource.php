<?php

namespace Eclipse\World\Filament\Clusters\World\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Eclipse\World\Filament\Clusters\World;
use Eclipse\World\Filament\Clusters\World\Resources\SpecialRegionMembershipResource\Pages;
use Eclipse\World\Models\Country;
use Eclipse\World\Models\CountrySpecialRegion;
use Eclipse\World\Models\Region;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SpecialRegionMembershipResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = CountrySpecialRegion::class;

    protected static ?string $slug = 'special-region-memberships';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $cluster = World::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['country', 'region']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('region_id')
                    ->label(__('eclipse-world::special-memberships.form.region.label'))
                    ->options(Region::where('is_special', true)->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('country_id')
                    ->label(__('eclipse-world::special-memberships.form.country.label'))
                    ->options(Country::orderBy('name')->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),

                DatePicker::make('start_date')
                    ->label(__('eclipse-world::special-memberships.form.start_date.label'))
                    ->required()
                    ->default(now()),

                DatePicker::make('end_date')
                    ->label(__('eclipse-world::special-memberships.form.end_date.label'))
                    ->after('start_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(50)
            ->defaultSort('start_date', 'desc')
            ->striped()
            ->columns([
                TextColumn::make('region.name')
                    ->label(__('eclipse-world::special-memberships.table.region.label'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('country.flag')
                    ->label(__('eclipse-world::special-memberships.table.flag.label'))
                    ->width(60),

                TextColumn::make('country.name')
                    ->label(__('eclipse-world::special-memberships.table.country.label'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->label(__('eclipse-world::special-memberships.table.start_date.label'))
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('eclipse-world::special-memberships.table.end_date.label'))
                    ->date()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label(__('eclipse-world::special-memberships.table.status.label'))
                    ->badge()
                    ->getStateUsing(function (CountrySpecialRegion $record) {
                        $today = now()->toDateString();

                        return match (true) {
                            $record->start_date > $today => 'future',
                            $record->end_date && $record->end_date < $today => 'ended',
                            default => 'active'
                        };
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'future' => 'warning',
                        'ended' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => __('eclipse-world::special-memberships.status.active'),
                        'future' => __('eclipse-world::special-memberships.status.future'),
                        'ended' => __('eclipse-world::special-memberships.status.ended'),
                    }),
            ])
            ->filters([
                SelectFilter::make('region_id')
                    ->label(__('eclipse-world::special-memberships.filters.region.label'))
                    ->options(Region::where('is_special', true)->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label(__('eclipse-world::special-memberships.filters.status.label'))
                    ->options([
                        'active' => __('eclipse-world::special-memberships.status.active'),
                        'future' => __('eclipse-world::special-memberships.status.future'),
                        'ended' => __('eclipse-world::special-memberships.status.ended'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        $today = now()->toDateString();

                        return match ($data['value']) {
                            'active' => $query->where('start_date', '<=', $today)
                                ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', $today)),
                            'future' => $query->where('start_date', '>', $today),
                            'ended' => $query->where('end_date', '<', $today),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->label(__('eclipse-world::special-memberships.actions.edit.label'))
                    ->modalHeading(__('eclipse-world::special-memberships.actions.edit.heading')),
                ActionGroup::make([
                    DeleteAction::make()
                        ->label(__('eclipse-world::special-memberships.actions.delete.label'))
                        ->modalHeading(__('eclipse-world::special-memberships.actions.delete.heading')),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('eclipse-world::special-memberships.actions.delete.label')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpecialRegionMemberships::route('/'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function getNavigationLabel(): string
    {
        return __('eclipse-world::special-memberships.nav_label');
    }

    public static function getBreadcrumb(): string
    {
        return __('eclipse-world::special-memberships.breadcrumb');
    }

    public static function getPluralModelLabel(): string
    {
        return __('eclipse-world::special-memberships.plural');
    }
}
