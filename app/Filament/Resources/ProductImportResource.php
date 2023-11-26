<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductImportResource\Pages;
use App\Filament\Resources\ProductImportResource\RelationManagers;
use App\Models\ProductImport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductImportResource extends Resource
{
    protected static ?string $model = ProductImport::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    
    protected static ?string $navigationLabel = 'Termék importok';

    protected static ?string $modelLabel = 'Termék import';

    protected static ?string $pluralModelLabel = 'Termék importok';

    protected static ?string $navigationGroup = 'Termékek';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('imported_by_id')
                    ->relationship('imported_by', 'name'),
                Forms\Components\TextInput::make('batch_id')
                    ->maxLength(36),
                Forms\Components\TextInput::make('records_counter')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('products_inserted')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('products_modified')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('brands_inserted')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('brands_modified')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('categories_inserted')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('categories_modified')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('fails_counter')
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255),
                Forms\Components\Textarea::make('data')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('file')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('finished_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('imported_by.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('batch_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('records_counter')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('products_inserted')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('products_modified')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brands_inserted')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brands_modified')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories_inserted')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories_modified')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fails_counter')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('file')
                    ->searchable(),
                Tables\Columns\TextColumn::make('finished_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductImports::route('/'),
            'create' => Pages\CreateProductImport::route('/create'),
            'view' => Pages\ViewProductImport::route('/{record}'),
            'edit' => Pages\EditProductImport::route('/{record}/edit'),
        ];
    }
}
