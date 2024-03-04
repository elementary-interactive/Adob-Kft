<?php

namespace App\Admin\Resources;

use App\Admin\Resources\BrandResource\Pages;
use App\Admin\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use App\Models\Status;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Neon\Models\Scopes\ActiveScope;
use Neon\Models\Statuses\BasicStatus;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationLabel = 'Márkák';

    protected static ?string $modelLabel = 'Márka';

    protected static ?string $pluralModelLabel = 'Márkák';

    protected static ?string $navigationGroup = 'Termékek';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('name')
                //     ->required()
                //     ->maxLength(255),
                // Forms\Components\TextInput::make('slug')
                //     ->required()
                //     ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->afterStateUpdated(function ($get, $set, ?string $state) {
                        if (!$get('is_slug_changed_manually') && filled($state)) {
                            $set('slug', Str::slug($state));
                        }
                    })
                    ->reactive()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->afterStateUpdated(function (Closure $set) {
                        $set('is_slug_changed_manually', true);
                    })
                    ->required(),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Kiemelt?')
                    ->required(),

                Forms\Components\Hidden::make('is_slug_changed_manually')
                    ->default(false)
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Név')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Termék URL a vágólapra másolva!')
                    ->copyableState(fn (string $state): string => route('brands.browse', ['brand' => $state])),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Kiemelt?')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\IconColumn::make('status')
                    ->label('Státusz')
                    ->icon(fn (BasicStatus $state): string => match ($state) {
                        BasicStatus::New      => 'heroicon-o-sparkles',
                        BasicStatus::Active   => 'heroicon-o-check-circle',
                        BasicStatus::Inactive => 'heroicon-o-x-circle',
                    })
                    ->color(fn (BasicStatus $state): string => match ($state) {
                        BasicStatus::New      => 'gray',
                        BasicStatus::Active   => 'success',
                        BasicStatus::Inactive => 'danger',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Utoljára módosítva')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->since(),
                // Tables\Columns\TextColumn::make('deleted_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(Status::class)
                    ->label('Státusz')
                    ->searchable(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('link')
                    ->label('Link')
                    ->icon('heroicon-o-link')
                    ->url(fn (Brand $record): string => route('brands.browse', ['brand' => $record->slug]))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('activate')
                    ->label('Márkák aktiválása')
                    ->action(fn (Collection $records) => $records->each->activate())
                    ->deselectRecordsAfterCompletion()
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),
                Tables\Actions\BulkAction::make('inactivate')
                    ->label('Márkák inaktiválása')
                    ->action(fn (Collection $records) => $records->each->inactivate())
                    ->deselectRecordsAfterCompletion()
                    ->color('danger')
                    ->icon('heroicon-o-x-circle'),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBrands::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                ActiveScope::class,
                SoftDeletingScope::class,
            ]);
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->name;
    }
}
