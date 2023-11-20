<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Neon\Models\Statuses\BasicStatus;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Termékek';

    protected static ?string $modelLabel = 'Termék';

    protected static ?string $pluralModelLabel = 'Termékek';

    protected static ?string $navigationGroup = 'Termékek';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                SelectTree::make('categories')
                    ->relationship('categories', 'name', 'parent_id', function ($query) {
                        return $query;
                    })
                    ->withCount()
                    ->independent(false)
                    ->expandSelected(true)
                    ->enableBranchNode()
                    // ->alwaysOpen(true)
                    ->searchable(),
                Forms\Components\Select::make('brand_id')
                    ->relationship('brand', 'name'),
                Forms\Components\TextInput::make('media_id')
                    ->numeric(),
                Forms\Components\TextInput::make('product_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('product_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('packaging')
                    ->maxLength(25),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('ean')
                    ->maxLength(13),
                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\Toggle::make('on_sale')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(1)
                    ->default('N'),
                Forms\Components\TextInput::make('og_data'),
                Forms\Components\TextInput::make('meta_data'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('id')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('parent_id')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('media_id')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('product_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand.name'),
                Tables\Columns\TextColumn::make('product_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('packaging')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ean')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\IconColumn::make('on_sale')
                    ->boolean(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(BasicStatus::Active->value)
                    ->searchable(),
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
                Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('brand')
                    ->relationship('brand', 'name')
                    ->label('Márka')
                    ->searchable(),
                Filter::make('tree')
                    ->form([
                        SelectTree::make('categories')
                            ->relationship('categories', 'name', 'parent_id')
                            ->independent(false)
                            ->enableBranchNode(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['categories'], function ($query, $categories) {
                            return $query->whereHas('categories', fn($query) => $query->whereIn('id', $categories));
                        });
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['categories']) {
                            return null;
                        }
             
                        return 'Kategóriák: ' . implode(', ', Category::whereIn('id', $data['categories'])->get()->pluck('name')->toArray());
                    }),
            ])
            ->actions([
                Tables\Actions\ReplicateAction::make(),
                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->paginated([25, 50, 100, 'all']);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
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
