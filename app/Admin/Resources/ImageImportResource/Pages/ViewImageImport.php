<?php

namespace App\Admin\Resources\ImageImportResource\Pages;

use App\Admin\Resources\ImageImportResource;
use App\Jobs\ADOBImageImportBatch_new;
use App\Models\ImageImport;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewImageImport extends ViewRecord
{
  protected static string $resource = ImageImportResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('index')
        ->label('Vissza')
        ->color('gray')
        ->icon('heroicon-o-arrow-small-left')
        ->url(fn (): string => route('filament.neon-admin.resources.image-imports.index')),
      // Actions\Action::make('restart')
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
    ];
  }
}
