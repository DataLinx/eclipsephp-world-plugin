<?php

namespace Eclipse\World\Filament\Clusters\World\Resources;

use Eclipse\Common\Filament\Concerns\HasCachedAbilityChecks;
use Eclipse\World\Filament\Clusters\World;
use Eclipse\World\Filament\Clusters\World\Resources\TariffCodeResource\Pages\ListTariffCodes;
use Eclipse\World\Models\TariffCode;
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
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class TariffCodeResource extends Resource
{
    use HasCachedAbilityChecks;
    use Translatable;

    protected static ?string $model = TariffCode::class;

    protected static ?string $slug = 'tariff-codes';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $cluster = World::class;

    public static function canUpdateAny(): bool
    {
        return static::canOnce('update_tariff_code');
    }

    public static function canDeleteAny(): bool
    {
        return static::canOnce('delete_tariff_code');
    }

    public static function canRestoreAny(): bool
    {
        return static::canOnce('restore_tariff_code');
    }

    public static function canForceDeleteAny(): bool
    {
        return static::canOnce('force_delete_tariff_code');
    }

    public static function canBulkDelete(): bool
    {
        return static::canOnce('delete_any_tariff_code');
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
        return $schema->components([
            TextInput::make('code')
                ->maxLength(20)
                ->required()
                ->unique(
                    table: 'world_tariff_codes',
                    column: 'code',
                    ignoreRecord: true,
                    modifyRuleUsing: function ($rule) {
                        return $rule->where('year', (int) date('Y'));
                    }
                )
                ->validationMessages([
                    'unique' => __('eclipse-world::tariff-codes.validation.code.unique'),
                ]),
            TextInput::make('name')
                ->label(__('eclipse-world::tariff-codes.form.name.label'))
                ->required(),
            TextInput::make('measure_unit')
                ->label(__('eclipse-world::tariff-codes.form.measure_unit.label'))
                ->nullable(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('code')
            ->striped()
            ->columns([
                TextColumn::make('code')->label(__('eclipse-world::tariff-codes.table.code.label'))->searchable()->sortable()->width(160),
                TextColumn::make('name')
                    ->label(__('eclipse-world::tariff-codes.table.name.label'))
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            $locale = app()->getLocale();

                            return $state[$locale] ?? reset($state);
                        }

                        return (string) $state;
                    })
                    ->searchable(),
                TextColumn::make('measure_unit')
                    ->label(__('eclipse-world::tariff-codes.form.measure_unit.label'))
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            $locale = app()->getLocale();

                            return $state[$locale] ?? reset($state);
                        }

                        return (string) $state;
                    })
                    ->toggleable()
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->authorize(fn () => self::canUpdateAny()),

                ActionGroup::make([
                    DeleteAction::make()
                        ->visible(fn (TariffCode $record) => ! $record->trashed())
                        ->authorize(fn () => self::canDeleteAny()),

                    RestoreAction::make()
                        ->visible(fn (TariffCode $record) => $record->trashed())
                        ->authorize(fn () => self::canRestoreAny()),

                    ForceDeleteAction::make()
                        ->visible(fn (TariffCode $record) => $record->trashed())
                        ->authorize(fn () => self::canForceDeleteAny()),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn () => self::canBulkDelete()),

                    RestoreBulkAction::make()
                        ->authorize(fn () => self::canRestoreAny()),

                    ForceDeleteBulkAction::make()
                        ->authorize(fn () => self::canForceDeleteAny()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTariffCodes::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
