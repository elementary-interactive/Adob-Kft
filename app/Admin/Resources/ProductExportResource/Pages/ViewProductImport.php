<?php

namespace App\Admin\Resources\ProductExportResource\Pages;

use App\Admin\Resources\ProductExportResource;
use App\Jobs\ADOBProductExportBatch_new;
use App\Models\ProductExport;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductExport extends ViewRecord
{
  protected static string $resource = ProductExportResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('index')
        ->label('Vissza')
        ->color('gray')
        ->icon('heroicon-o-arrow-small-left')
        ->url(fn (): string => route('filament.neon-admin.resources.product-exports.index')),
      Actions\Action::make('restart')
        ->label('Újra')
        ->icon('heroicon-o-arrow-path')
        ->requiresConfirmation()
        ->color('warning')
        ->action(function (ProductExport $record) {
          $new = ProductExport::create([
            // 'data'  => $record['data'],
            'file'           => $record['file'],
            'exported_by_id' => auth()->user()->id,
            'satus'          => 'waiting'
          ]);
          ADOBProductExportBatch_new::dispatch($new);
        }),
    ];
  }
}
