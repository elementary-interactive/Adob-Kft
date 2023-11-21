<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Closure;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
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
                Section::make()
                    ->schema([
                        Group::make()
                        ->schema([
                            Forms\Components\TextInput::make('ean')
                                ->label('Vonalkód')
                                ->columnSpan(['s' => 1]),
                            Forms\Components\TextInput::make('product_id')
                                ->label('Cikkszám')
                                ->columnSpan(['s' => 1]),
                        ])
                        ->columns(2),
                        SpatieMediaLibraryFileUpload::make(Product::MEDIA_COLLECTION)
                            ->label('Képek')
                            ->collection(Product::MEDIA_COLLECTION)
                            ->multiple()
                            ->imageEditor()
                            ->conversion('thumb')
                            ->downloadable()
                            ->enableReordering(),
                        Forms\Components\TextInput::make('name')
                            ->label('Név')
                            ->afterStateUpdated(function ($get, $set, ?string $state) {
                                if (!$get('is_slug_changed_manually') && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->reactive()       
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL')
                            ->afterStateUpdated(function (Closure $set) {
                                $set('is_slug_changed_manually', true);
                            })
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description')
                            ->label('Leírás')
                            ->columnSpanFull()
                            ->required(),
                        Forms\Components\Textarea::make('packaging')
                            ->label('Csomagolás információk')
                            ->columnSpanFull(),
                        Group::make()
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('Ár')
                                    ->helperText('Ajánlott nettó fogasztói ár.')
                                    ->numeric()
                                    ->prefix('Ft'),
                                Forms\Components\Toggle::make('on_sale')
                                    ->label('Jelenleg akciós')
                                    ->inline(false),
                            ]),
                        Forms\Components\TextInput::make('product_number')
                            ->maxLength(255),
                        Forms\Components\KeyValue::make('og_data'),
                        Forms\Components\KeyValue::make('meta_data'),
                        Forms\Components\Hidden::make('is_slug_changed_manually')
                            ->default(false)
                            ->dehydrated(false),
                    ])
                    ->columnSpan(['lg' => 2]),
                Section::make()
                    ->schema([
                    Select::make('status')
                        ->label('Státusz')
                        ->required()
                        ->options([
                            BasicStatus::New->value      => 'Új',
                            BasicStatus::Active->value   => 'Aktív',
                            BasicStatus::Inactive->value => 'Inaktív',
                        ]),
                    Section::make('Kapcsolatok')
                        ->description('A termék márka és katergória kapcsolatai.')
                        ->schema([
                            Forms\Components\Select::make('brand_id')
                                ->label('Márka')
                                ->required()
                                ->relationship('brand', 'name')
                                ->columnSpan([
                                    'sm' => 2,
                                    'xl' => 3,
                                    '2xl' => 4,
                                ]),
                            SelectTree::make('categories')
                                ->label('Kategóriák')
                                ->required()
                                ->relationship('categories', 'name', 'parent_id', function ($query) {
                                    return $query;
                                })
                                ->withCount()
                                ->independent(true)
                                ->expandSelected(true)
                                ->enableBranchNode()
                                // ->alwaysOpen(true)
                                ->searchable()
                                ->columnSpan([
                                    'sm' => 2,
                                    'xl' => 3,
                                    '2xl' => 4,
                                ]),
                        ])
                    ])
                    ->columnSpan(['lg' => 1])
            ])
            ->columns(3);
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
                    ->label('Cikkszám')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-clipboard-document-check')
                    ->iconPosition(IconPosition::After)
                    ->copyable()
                    ->copyMessage('Termék URL a vágólapra másolva!')
                    ->copyableState(fn (Product $record): string => route('product.show', ['slug' => $record->slug])),
                Tables\Columns\ImageColumn::make('images')
                    ->label('Képek')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(isSeparate: true)
                    ->toggleable(),
                    // ->extraImgAttributes(['loading' => 'lazy']),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Márka'),
                // Tables\Columns\TextColumn::make('product_number')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Név')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                // Tables\Columns\TextColumn::make('packaging')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('ean')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('price')
                //     ->money()
                //     ->sortable(),
                // Tables\Columns\IconColumn::make('on_sale')
                //     ->boolean(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Státusz')
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
                Tables\Actions\ReplicateAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        /** Prepend COPY_TAG...
                         */
                        $data['name']        = \App\Models\Product::COPY_TAG . $data['name'];
                        $data['product_id']  = \App\Models\Product::COPY_TAG . $data['product_id'];
                        $data['status']      = \Neon\Models\Statuses\BasicStatus::Inactive->value;

                        dd($data);
                        return $data;
                    })
                    ->successRedirectUrl(fn (Product $replica): string => route('filament.admin2.resources.products.edit', [
                        'record' => $replica,
                    ]))
                    ->successNotificationTitle('Termék sikeresen duplikálva.'),
                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->paginated([100, 50, 25, 'all']);
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
