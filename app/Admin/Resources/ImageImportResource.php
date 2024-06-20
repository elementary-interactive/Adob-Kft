<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ImageImportResource\Pages;
use App\Admin\Resources\ImageImportResource\RelationManagers;
use App\Jobs\ADOBImageImportBatch;
use App\Jobs\ADOBImageImportBatch_new;
use App\Models\ImageImport;
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
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ImageImportResource extends Resource
{
  protected static ?string $model = ImageImport::class;

  protected static ?string $navigationIcon = 'heroicon-o-photo';

  protected static ?string $navigationLabel = 'Képek importálása';

  protected static ?string $modelLabel = 'Kép import';

  protected static ?string $pluralModelLabel = 'Kép importálások';

  protected static ?string $navigationGroup = 'Importok / Exportok';

  protected static ?int $navigationSort = 6;

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
        // ->getStateUsing(fn (string $state): string => match ($state) {
        //     'waiting' => 'Várakozik...',
        //     'failed' => 'Hibás futás',
        //     'finished' => 'Sikeresen lefutott',
        //     'running' => 'Fut...',
        // }),
        Infolists\Components\TextEntry::make('job')
          ->label('Feladat / Hibaüzenet')
          ->columnSpanFull()
          ->getStateUsing(function (ImageImport $record) {
            return $record->job; 
          })
          ->html(),
        Infolists\Components\TextEntry::make('file')
          ->label('Állomány')
          ->icon('heroicon-o-arrow-down-on-square')
          ->iconPosition(IconPosition::Before)
          ->getStateUsing(function (ImageImport $record) {
            return '<a href="' . Storage::url($record->file) . '" target="_blank">' . $record->file . '</a>';
          })
          ->html()
      ]);
  }

  public static function form(Form $form): Form
  {
      return $form
      ->schema([
        Forms\Components\FileUpload::make('images')
          ->image() 
          ->multiple()
          ->preserveFilenames()
          ->disk(config('filesystems.default'))
          ->directory('imports')
          ->helperText('A képek elnevezésénél a {cikkszám}_{kép sorszáma} képlet szerint kell eljárni.')
      ]);
  }

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
          ->getStateUsing(function (ImageImport $record) {
            return ($record->records_counter > 0) ? intval((($record->records_handled) / $record->records_counter) * 100) : 0;
          }),
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
        // Tables\Actions\Action::make('restart')
        //   ->label('Újra')
        //   ->icon('heroicon-o-arrow-path')
        //   ->requiresConfirmation()
        //   ->color('warning')
        //   ->action(function (ImageImport $record) {
        //     $new = ImageImport::create([
        //       // 'data'  => $record['data'],
        //       'file'           => $record['file'],
        //       'imported_by_id' => auth()->user()->id,
        //       'satus'          => 'waiting'
        //     ]);
        //     ADOBImageImportBatch_new::dispatch($new);
        //   }),
        Tables\Actions\ViewAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ])
      ->defaultSort('created_at', 'desc')
      ->poll('5s');
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
      'index'   => Pages\ListImageImports::route('/'),
      'create'  => Pages\CreateImageImport::route('/create'),
      'view'    => Pages\ViewImageImport::route('/{record}'),
      'edit'    => Pages\EditImageImport::route('/{record}/edit'),
    ];
  }
}
