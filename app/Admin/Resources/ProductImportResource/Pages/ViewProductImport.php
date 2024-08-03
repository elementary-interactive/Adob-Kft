<?php

namespace App\Admin\Resources\ProductImportResource\Pages;

use App\Admin\Resources\ProductImportResource;
use App\Jobs\ADOBNotifyJob;
use App\Jobs\ADOBProductImportBatch_new;
use App\Models\ProductImport;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Bus;

class ViewProductImport extends ViewRecord
{
  protected static string $resource = ProductImportResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\Action::make('index')
        ->label('Vissza')
        ->color('gray')
        ->icon('heroicon-o-arrow-small-left')
        ->url(fn (): string => route('filament.neon-admin.resources.product-imports.index')),
      Actions\Action::make('restart')
        ->label('Újra')
        ->icon('heroicon-o-arrow-path')
        ->requiresConfirmation()
        ->color('warning')
        ->action(function (ProductImport $record) {
          Bus::batch([
            new ADOBNotifyJob()
          ])
            ->name('product_import');
          $new = ProductImport::create([
            // 'data'  => $record['data'],
            'file'           => $record['file'],
            'imported_by_id' => auth()->user()->id,
            'satus'          => 'waiting'
          ]);
          ADOBProductImportBatch_new::dispatch($new);
        }),
    ];
  }
}
