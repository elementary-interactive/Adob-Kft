<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ProductResource\Pages;
use App\Admin\Resources\ProductResource\RelationManagers;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImport;
use App\Models\Status;
use Closure;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Neon\Models\Scopes\ActiveScope;
use Neon\Models\Statuses\BasicStatus;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Termékek';

    protected static ?string $modelLabel = 'Termék';

    protected static ?string $pluralModelLabel = 'Termékek';

    protected static ?string $navigationGroup = 'Termékek';

    protected static ?int $navigationSort = 3;

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
                                    ->columnSpan([
                                        'sm' => 2,
                                        'xl' => 4,
                                        '2xl' => 6,
                                    ]),
                                Forms\Components\TextInput::make('product_id')
                                    ->label('Cikkszám')
                                    ->columnSpan([
                                        'sm' => 2,
                                        'xl' => 4,
                                        '2xl' => 6,
                                    ]),
                                Select::make('status')
                                    ->label('Státusz')
                                    ->required()
                                    ->reactive()
                                    ->default(Status::New)
                                    ->options(Status::class)
                                    ->columnSpan([
                                        'sm' => 1,
                                        'xl' => 2,
                                        '2xl' => 3,
                                    ]),
                            ])
                            ->columns([
                                'sm' => 5,
                                'xl' => 10,
                                '2xl' => 15,
                            ]),
                        \App\Forms\Components\ADOBMediaHandler::make(Product::MEDIA_COLLECTION)
                            // SpatieMediaLibraryFileUpload::make(Product::MEDIA_COLLECTION)
                            ->label('Képek')
                            ->collection(Product::MEDIA_COLLECTION)
                            ->multiple()
                            ->imageEditor()
                            ->panelLayout('grid')
                            ->responsiveImages()
                            ->downloadable()
                            ->previewable()
                            ->disk('public')
                            ->reorderable()
                            ->reactive()
                            ->allowPaste(true)
                            ->appendFiles(true)
                            ->afterStateUpdated(function (string $operation, Get $get, ?array $state, ?array $old, ?string $model) {

                                /** Getting the product object and attach media files. 
                                 */
                                $obj = $model::find($get('id'));

                                foreach ($state as $file) {
                                    try {
                                        if (gettype($file) == \Livewire\Features\SupportFileUploads\TemporaryUploadedFile::class) {
                                            $x = $obj->addMedia($file)
                                                ->toMediaCollection($model::MEDIA_COLLECTION);
                                            dump($x, $model, $obj);
                                        }
                                    } catch (\Exception $e) {
                                        dd($e);
                                    }
                                }

                                $get('../edit-product')?->fillForm();
                            }),
                        // \App\Forms\Components\PasteField::make(Product::MEDIA_COLLECTION),
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
                            ->afterStateUpdated(function ($set) {
                                $set('is_slug_changed_manually', true);
                            })
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description')
                            ->label('Leírás')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('packaging')
                            ->label('Csomagolás információk')
                            ->columnSpanFull(),
                        Fieldset::make()
                            ->label('Kapcsolatok')
                            // ->description('A termék márka és katergória kapcsolatai.')
                            ->schema([
                                Forms\Components\Select::make('brand_id')
                                    ->label('Márka')
                                    ->required()
                                    ->native(false)
                                    ->relationship('brand', 'name')
                                    ->columnSpan([
                                        'sm' => 2,
                                        'xl' => 3,
                                        '2xl' => 4,
                                    ]),
                                Forms\Components\Select::make('category_id')
                                    ->label('Kategóriák')
                                    ->required()
                                    ->multiple()
                                    ->native(false)
                                    ->relationship('categories', 'name')
                                    ->options(fn (): array => Category::getNestedFlat()) //('name', 'id', '—', ' '))
                                    ->columnSpan([
                                        'sm' => 2,
                                        'xl' => 3,
                                        '2xl' => 4,
                                    ]),
                                // SelectTree::make('categories')
                                //     ->label('Kategóriák')
                                //     ->required()
                                //     ->relationship('categories', 'name', 'parent_id', function ($query) {
                                //         return $query;
                                //     })
                                //     ->withCount()
                                //     ->independent(true)
                                //     ->expandSelected(true)
                                //     ->enableBranchNode()
                                //     // ->alwaysOpen(true)
                                //     ->searchable()
                                //     ->columnSpan([
                                //         'sm' => 2,
                                //         'xl' => 3,
                                //         '2xl' => 4,
                                //     ]),
                            ]),
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
                    ]),
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
                    ->copyMessage('Termék URL a vágólapra másolva!'),
                // ->copyableState(fn (Product $record): string => route('product.show', ['slug' => $record->slug])),
                Tables\Columns\ImageColumn::make('images')
                    ->label('Képek')
                    // ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText(isSeparate: true)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Márka'),
                Tables\Columns\TextColumn::make('product_number')
                    ->searchable()
                    ->label('Termék száma')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Columns\TextColumn::make('images_count')
                    ->label('Képek száma')
                    ->numeric()
                    ->getStateUsing(function (Model $record): int {
                        return $record->getMedia(Product::MEDIA_COLLECTION)->count();
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Létrehozva')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Utoljára frissítve')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Törlés dátuma')
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
                SelectFilter::make('status')
                    ->options(Status::class)
                    ->label('Státusz')
                    ->searchable(),
                Filter::make('tree')
                    ->label('Kategória')
                    ->form([
                        SelectTree::make('categories')
                            ->relationship('categories', 'name', 'parent_id')
                            ->independent(false)
                            ->enableBranchNode(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['categories'], function ($query, $categories) {
                            return $query->whereHas('categories', fn ($query) => $query->whereIn('id', $categories));
                        });
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['categories']) {
                            return null;
                        }

                        return 'Kategóriák: ' . implode(', ', Category::whereIn('id', $data['categories'])->get()->pluck('name')->toArray());
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('link')
                    ->label('Link')
                    ->icon('heroicon-o-link')
                    ->url(fn (Product $record): string => route('product.show', ['slug' => $record->slug]))
                    ->openUrlInNewTab(),
                Tables\Actions\ReplicateAction::make('replica')
                    ->beforeReplicaSaved(function (Product $replica): void {
                        $replica->name          = $replica->name . \App\Models\Product::COPY_TAG;
                        $replica->slug          = $replica->slug . \App\Models\Product::COPY_TAG;
                        $replica->product_id    = $replica->product_id . \App\Models\Product::COPY_TAG;
                        $replica->status = BasicStatus::Inactive->value;
                        // Runs after the record has been replicated but before it is saved to the database.
                    })
                    ->successRedirectUrl(fn (Product $replica): string => route('filament.neon-admin.resources.products.edit', [
                        'record' => $replica,
                    ]))
                    ->successNotificationTitle('Termék sikeresen duplikálva.'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('activate')
                    ->label('Termékek aktiválása')
                    ->action(fn (Collection $records) => $records->each->activate())
                    ->deselectRecordsAfterCompletion()
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),
                Tables\Actions\BulkAction::make('inactivate')
                    ->label('Termékek inaktiválása')
                    ->action(fn (Collection $records) => $records->each->inactivate())
                    ->deselectRecordsAfterCompletion()
                    ->color('danger')
                    ->icon('heroicon-o-x-circle'),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->paginated([25, 50, 100, 'all'])
            ->defaultPaginationPageOption(25)
            ->defaultSort('created_at', 'desc');
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s');
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
                ActiveScope::class,
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['product_number', 'product_id', 'name', 'slug', 'description', 'packaging', 'ean', 'brand.name', 'categories.name'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->name;
    }
}
