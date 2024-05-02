<?php

namespace App\Admin\Resources\ProductExportResource\Pages;

use App\Admin\Resources\ProductExportResource;
use App\Jobs\ADOBProductExportBatch_new;
use App\Models\ProductExport;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Validator;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListProductExports extends ListRecords
{
    protected static string $resource = ProductExportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('ADOB_batch_export')
                ->label('Exportálás')
                ->icon('heroicon-o-arrow-up-tray')
                // ->modalHeading('Exportálás')
                // ->modalDescription('ADOB Excel file exportálása.')
                // ->modalSubmitActionLabel('Exportálás')
                // ->modalIcon('heroicon-o-arrow-up-tray')
                ->action(function (array $data, array $arguments): void {
                    $exporter = new ProductExport();
                    /** Add authenticated user...
                     */
                    $exporter->exported_by()->associate(auth()->user());
                    $exporter->save();

                    ADOBProductExportBatch_new::dispatch($exporter);
                }),
            // ->slideOver(),
        ];
    }
}
