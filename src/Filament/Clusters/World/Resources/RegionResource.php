<?php

namespace Eclipse\World\Filament\Clusters\World\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Eclipse\World\Filament\Clusters\World;
use Eclipse\World\Filament\Clusters\World\Resources\RegionResource\Pages;
use Eclipse\World\Models\Region;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Region::class;

    protected static ?string $slug = 'regions';

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?string $cluster = World::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->defaultPaginationPageOption(50)
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
                    ->counts('countries')
                    ->sortable(),

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
            ->actions([
                EditAction::make()
                    ->label(__('eclipse-world::regions.actions.edit.label'))
                    ->modalHeading(__('eclipse-world::regions.actions.edit.heading')),
                ActionGroup::make([
                    DeleteAction::make()
                        ->label(__('eclipse-world::regions.actions.delete.label'))
                        ->modalHeading(__('eclipse-world::regions.actions.delete.heading')),
                    RestoreAction::make()
                        ->label(__('eclipse-world::regions.actions.restore.label'))
                        ->modalHeading(__('eclipse-world::regions.actions.restore.heading')),
                    ForceDeleteAction::make()
                        ->label(__('eclipse-world::regions.actions.force_delete.label'))
                        ->modalHeading(__('eclipse-world::regions.actions.force_delete.heading'))
                        ->modalDescription(fn (Region $record): string => __('eclipse-world::regions.actions.force_delete.description', [
                            'name' => $record->name,
                        ])),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('eclipse-world::regions.actions.delete.label')),
                    RestoreBulkAction::make()
                        ->label(__('eclipse-world::regions.actions.restore.label')),
                    ForceDeleteBulkAction::make()
                        ->label(__('eclipse-world::regions.actions.force_delete.label')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegions::route('/'),
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
