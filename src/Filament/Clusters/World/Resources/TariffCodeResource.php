<?php

namespace Eclipse\World\Filament\Clusters\World\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Eclipse\World\Filament\Clusters\World;
use Eclipse\World\Models\TariffCode;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
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

class TariffCodeResource extends Resource implements HasShieldPermissions
{
    use Translatable;

    protected static ?string $model = TariffCode::class;

    protected static ?string $slug = 'tariff-codes';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $cluster = World::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
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
            ->defaultPaginationPageOption(50)
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
            ->actions([
                EditAction::make(),
                ActionGroup::make([
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Eclipse\World\Filament\Clusters\World\Resources\TariffCodeResource\Pages\ListTariffCodes::route('/'),
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
}
