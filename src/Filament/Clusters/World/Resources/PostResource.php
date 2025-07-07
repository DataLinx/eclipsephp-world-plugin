<?php

namespace Eclipse\World\Filament\Clusters\World\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Eclipse\World\Filament\Clusters\World;
use Eclipse\World\Models\Post;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class PostResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Post::class;

    protected static ?string $slug = 'posts';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = World::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->actions([
                EditAction::make()
                    ->label(__('eclipse-world::posts.actions.edit.label'))
                    ->modalHeading(__('eclipse-world::posts.actions.edit.heading')),
                ActionGroup::make([
                    DeleteAction::make()
                        ->label(__('eclipse-world::posts.actions.delete.label'))
                        ->modalHeading(__('eclipse-world::posts.actions.delete.heading')),
                    RestoreAction::make()
                        ->label(__('eclipse-world::posts.actions.restore.label'))
                        ->modalHeading(__('eclipse-world::posts.actions.restore.heading')),
                    ForceDeleteAction::make()
                        ->label(__('eclipse-world::posts.actions.force_delete.label'))
                        ->modalHeading(__('eclipse-world::posts.actions.force_delete.heading'))
                        ->modalDescription(fn (Post $record): string => __('eclipse-world::posts.actions.force_delete.description', [
                            'name' => $record->name,
                        ])),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('eclipse-world::posts.actions.delete.label')),
                    RestoreBulkAction::make()
                        ->label(__('eclipse-world::posts.actions.restore.label')),
                    ForceDeleteBulkAction::make()
                        ->label(__('eclipse-world::posts.actions.force_delete.label')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => PostResource\Pages\ListPosts::route('/'),
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
