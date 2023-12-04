<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductImportResource\Pages;
use App\Filament\Resources\ProductImportResource\RelationManagers;
use App\Jobs\ADOBProductImportBatch;
use App\Models\ProductImport;
use App\Tables\Columns\ProgressColumn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImportResource extends Resource
{
    protected static ?string $model = ProductImport::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    
    protected static ?string $navigationLabel = 'Termék importok';

    protected static ?string $modelLabel = 'Termék import';

    protected static ?string $pluralModelLabel = 'Termék importok';

    protected static ?string $navigationGroup = 'Importok / Exportok';

    protected static ?int $navigationSort = 4;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Kezdete')
                    ->dateTime('Y M j H:i:s'),
                Infolists\Components\TextEntry::make('imported_by.name')
                    ->label('Importálta'),
                Infolists\Components\TextEntry::make('finished_at')
                    ->label('Vége')
                    ->dateTime('Y M j H:i:s'),
                Infolists\Components\TextEntry::make('status')
                    ->label('Státusz')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'waiting' => 'gray',
                        'failed' => 'warning',
                        'finished' => 'success',
                        'running' => 'info',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'waiting' => 'heroicon-o-ellipsis-horizontal-circle',
                        'running' => 'heroicon-o-play-circle',
                        'finished' => 'heroicon-o-check-circle',
                        'failed' => 'heroicon-o-exclamation-circle',
                    }),
                Infolists\Components\Fieldset::make('Statisztikai adatok')
                    ->schema([
                        Infolists\Components\TextEntry::make('records_counter')
                            ->label('Rekordok száma')
                            ->weight(FontWeight::Bold)
                            ->numeric(),
                        Infolists\Components\TextEntry::make('products_inserted')
                            ->label('Termék rekodok száma')
                            ->getStateUsing(function(ProductImport $record) {
                                return $record->products_inserted + $record->products_modified;
                            })
                            ->numeric(),
                        Infolists\Components\TextEntry::make('brands_inserted')
                            ->label('Márkák száma')
                            ->getStateUsing(function(ProductImport $record) {
                                return $record->brands_inserted + $record->brands_modified;
                            })
                            ->numeric(),
                        Infolists\Components\TextEntry::make('categories_inserted')
                            ->label('Kategóriák száma')
                            ->getStateUsing(function(ProductImport $record) {
                                return $record->categories_inserted + $record->categories_modified;
                            })
                            ->numeric(),
                        Infolists\Components\TextEntry::make('fails_counter')
                            ->color('danger')
                            ->label('Hibák száma')
                            ->numeric(),
                    ])
                    ->columns(5),
                
                    // ->getStateUsing(fn (string $state): string => match ($state) {
                    //     'waiting' => 'Várakozik...',
                    //     'failed' => 'Hibás futás',
                    //     'finished' => 'Sikeresen lefutott',
                    //     'running' => 'Fut...',
                    // }),
                Infolists\Components\TextEntry::make('job')
                    ->label('Feladat / Hibaüzenet')
                    ->columnSpanFull(),
                Infolists\Components\TextEntry::make('file')
                    ->label('Állomány')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->iconPosition(IconPosition::Before)
                    ->getStateUsing(function (ProductImport $record) {
                        return '<a href="'.Storage::url($record->file).'" target="_blank">'.$record->file.'</a>';
                    })
                    ->html()
            ]);
    }

    // public static function form(Form $form): Form
    // {
    //     return $form
    //     ->schema([
    //         Forms\Components\Group::make()
    //             ->schema([
    //                 Forms\Components\Select::make('imported_by_id')
    //                 ->relationship('imported_by', 'name'),
    //             Forms\Components\TextInput::make('records_counter')
    //                 ->label('Rekordok száma')
    //                 ->required()
    //                 ->numeric()
    //                 ->default(0),
    //             // Forms\Components\TextInput::make('products_inserted')
    //             //     ->required()
    //             //     ->numeric(),
    //             // Forms\Components\TextInput::make('products_modified')
    //             //     ->required()
    //             //     ->numeric(),
    //             // Forms\Components\TextInput::make('brands_inserted')
    //             //     ->required()
    //             //     ->numeric(),
    //             // Forms\Components\TextInput::make('brands_modified')
    //             //     ->required()
    //             //     ->numeric(),
    //             // Forms\Components\TextInput::make('categories_inserted')
    //             //     ->required()
    //             //     ->numeric(),
    //             // Forms\Components\TextInput::make('categories_modified')
    //             //     ->required()
    //             //     ->numeric(),
    //             Forms\Components\TextInput::make('fails_counter')
    //                 ->label('Hibák száma')
    //                 ->numeric(),
    //             Forms\Components\Textarea::make('job')
    //                 ->label('Státusz/Hibaüzenet'),
    //             Forms\Components\TextInput::make('status')
    //                 ->maxLength(255),
    //             Forms\Components\Textarea::make('data')
    //                 ->columnSpanFull(),
    //             Forms\Components\TextInput::make('file')
    //                 ->maxLength(255),
    //                 // Forms\Components\Section::make('Inventory')
    //                 //     ->schema([
    //                 //         Forms\Components\TextInput::make('sku')
    //                 //             ->label('SKU (Stock Keeping Unit)')
    //                 //             ->unique(Product::class, 'sku', ignoreRecord: true)
    //                 //             ->required(),

    //                 //         Forms\Components\TextInput::make('barcode')
    //                 //             ->label('Barcode (ISBN, UPC, GTIN, etc.)')
    //                 //             ->unique(Product::class, 'barcode', ignoreRecord: true)
    //                 //             ->required(),

    //                 //         Forms\Components\TextInput::make('qty')
    //                 //             ->label('Quantity')
    //                 //             ->numeric()
    //                 //             ->rules(['integer', 'min:0'])
    //                 //             ->required(),

    //                 //         Forms\Components\TextInput::make('security_stock')
    //                 //             ->helperText('The safety stock is the limit stock for your products which alerts you if the product stock will soon be out of stock.')
    //                 //             ->numeric()
    //                 //             ->rules(['integer', 'min:0'])
    //                 //             ->required(),
    //                 //     ])
    //                 //     ->columns(2),

    //             ])
    //             ->columnSpan(['lg' => 2]),

    //         Forms\Components\Group::make()
    //             ->schema([
    //                 Forms\Components\Section::make('Állapot')
    //                     ->schema([
    //                         Forms\Components\TextInput::make('status')
    //                             ->label('Állapot')
    //                             ->default(true),

    //                         Forms\Components\DatePicker::make('created_at')
    //                             ->label('Elindítva')
    //                             ->readOnly(),
    //                         Forms\Components\DatePicker::make('finished_at')
    //                             ->label('Befejezve')
    //                             ->readOnly(),
    //                     ]),
    //             ])
    //             ->columnSpan(['lg' => 1]),
    //     ])
    //     ->columns(3);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Indítva')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('finished_at')
                    ->label('Befejezve')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Státusz')
                    ->icon(fn (string $state): string => match ($state) {
                        'waiting' => 'heroicon-o-ellipsis-horizontal-circle',
                        'running' => 'heroicon-o-play-circle',
                        'finished' => 'heroicon-o-check-circle',
                        'failed' => 'heroicon-o-exclamation-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'waiting' => 'gray',
                        'running' => 'info',
                        'finished' => 'success',
                        'failed' => 'danger',

                        default => 'gray'
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('imported_by.name')
                    ->label('Indította')
                    ->searchable(),
                ProgressColumn::make('progress')
                    ->label('Folyamat')
                    ->getStateUsing(function (ProductImport $record) {
                        // dd($record->products_modified, intval((($record->products_inserted + $record->products_modified) / $record->records_counter) * 100));
                        return ($record->records_counter > 0) ? intval((($record->products_inserted + $record->products_modified) / $record->records_counter) * 100) : 0;
                    }),
                Tables\Columns\TextColumn::make('job')
                    ->label('')
                    ->getStateUsing(function (ProductImport $record) {
                        return $record->records_counter.'/'.($record->products_inserted + $record->products_modified).' termék, '.($record->categories_inserted + $record->categories_modified).' kategória, '.($record->brands_inserted + $record->brands_modified).' márka';
                    })
                    ->description(fn (ProductImport $record): string => Str::limit($record->job, 50) ?: ''),
                Tables\Columns\TextColumn::make('file')
                    ->label('Állomány')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->iconPosition(IconPosition::Before)
                    ->getStateUsing(function (ProductImport $record) {
                        return '<a href="'.Storage::url($record->file).'" target="_blank">'.substr($record->file, 13).'</a>';
                    })
                    ->html()
                    ->size(Tables\Columns\TextColumn\TextColumnSize::ExtraSmall)
                    ->searchable(),
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
                Tables\Actions\Action::make('restart')
                    ->label('Újra')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->color('warning')
                    ->action(function(ProductImport $record) {
                        $new = ProductImport::create([
                            'data'  => $record['data'],
                            'file'  => $record['file'],
                            'imported_by_id'    => $record['imported_by_id'],
                            'satus' => 'waiting'
                        ]);
                        ADOBProductImportBatch::dispatch($new);
                    }),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('10s');;
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
