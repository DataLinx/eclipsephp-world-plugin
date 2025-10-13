<?php

namespace Eclipse\World\Filament\Clusters\World\Resources;

use Eclipse\Common\Filament\Concerns\HasCachedAbilityChecks;
use Eclipse\World\Filament\Clusters\World;
use Eclipse\World\Filament\Clusters\World\Resources\PostResource\Pages\ListPosts;
use Eclipse\World\Models\Post;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class PostResource extends Resource
{
    use HasCachedAbilityChecks;

    protected static ?string $model = Post::class;

    protected static ?string $slug = 'posts';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = World::class;

    public static function canUpdateAny(): bool
    {
        return static::canOnce('update_post');
    }

    public static function canDeleteAny(): bool
    {
        return static::canOnce('delete_post');
    }

    public static function canRestoreAny(): bool
    {
        return static::canOnce('restore_post');
    }

    public static function canForceDeleteAny(): bool
    {
        return static::canOnce('force_delete_post');
    }

    public static function canBulkDelete(): bool
    {
        return static::canOnce('delete_any_post');
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
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->required()
                    ->label(__('eclipse-world::posts.form.country_id.label'))
                    ->live(),

                TextInput::make('code')
                    ->required()
                    ->label(__('eclipse-world::posts.form.code.label'))
                    ->rules(function (Get $get, ?Post $record) {
                        return [
                            'required',
                            'string',
                            Rule::unique('world_posts', 'code')
                                ->where('country_id', $get('country_id'))
                                ->ignore($record?->id),
                        ];
                    })
                    ->validationMessages([
                        'unique' => __('eclipse-world::posts.validation.unique_country_code'),
                    ]),

                TextInput::make('name')
                    ->required()
                    ->label(__('eclipse-world::posts.form.name.label')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('country.name')
                    ->label(__('eclipse-world::posts.table.country.label'))
                    ->formatStateUsing(fn (string $state, Post $record) => trim("{$record->country->flag} {$state}"))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label(__('eclipse-world::posts.table.code.label')),

                TextColumn::make('name')
                    ->label(__('eclipse-world::posts.table.name.label'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('country_id')
                    ->label(__('eclipse-world::posts.filter.country.label'))
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label(__('eclipse-world::posts.actions.edit.label'))
                    ->modalHeading(__('eclipse-world::posts.actions.edit.heading'))
                    ->authorize(fn () => self::canUpdateAny()),

                ActionGroup::make([
                    DeleteAction::make()
                        ->label(__('eclipse-world::posts.actions.delete.label'))
                        ->modalHeading(__('eclipse-world::posts.actions.delete.heading'))
                        ->visible(fn (Post $record) => ! $record->trashed())
                        ->authorize(fn () => self::canDeleteAny()),

                    RestoreAction::make()
                        ->label(__('eclipse-world::posts.actions.restore.label'))
                        ->modalHeading(__('eclipse-world::posts.actions.restore.heading'))
                        ->visible(fn (Post $record) => $record->trashed())
                        ->authorize(fn () => self::canRestoreAny()),

                    ForceDeleteAction::make()
                        ->label(__('eclipse-world::posts.actions.force_delete.label'))
                        ->modalHeading(__('eclipse-world::posts.actions.force_delete.heading'))
                        ->modalDescription(fn (Post $record): string => __('eclipse-world::posts.actions.force_delete.description', [
                            'name' => $record->name,
                        ]))
                        ->visible(fn (Post $record) => $record->trashed())
                        ->authorize(fn () => self::canForceDeleteAny()),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('eclipse-world::posts.actions.delete.label'))
                        ->authorize(fn () => self::canBulkDelete()),

                    RestoreBulkAction::make()
                        ->label(__('eclipse-world::posts.actions.restore.label'))
                        ->authorize(fn () => self::canRestoreAny()),

                    ForceDeleteBulkAction::make()
                        ->label(__('eclipse-world::posts.actions.force_delete.label'))
                        ->authorize(fn () => self::canForceDeleteAny()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with('country');
    }

    public static function getNavigationLabel(): string
    {
        return __('eclipse-world::posts.nav_label');
    }

    public static function getBreadcrumb(): string
    {
        return __('eclipse-world::posts.breadcrumb');
    }

    public static function getPluralModelLabel(): string
    {
        return __('eclipse-world::posts.plural');
    }
}
