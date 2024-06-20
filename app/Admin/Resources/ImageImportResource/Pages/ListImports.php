<?php

namespace App\Admin\Resources\ImageImportResource\Pages;

use App\Admin\Resources\ImageImportResource;
use App\Jobs\ADOBImageImportBatch_new;
use App\Models\ImageImport;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Validator;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListImageImports extends ListRecords
{
    protected static string $resource = ImageImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\Action::make('ADOB_batch_import')
            //     ->label('Importálás')
            //     ->modalHeading('Importálás')
            //     ->modalDescription('ADOB Excel file importálása.')
            //     ->modalSubmitActionLabel('Importálás')
            //     ->icon('heroicon-o-arrow-down-tray')
            //     ->modalIcon('heroicon-o-arrow-down-tray')
            //     ->form([
            //         Forms\Components\Toggle::make('header')
            //             ->label('Fejléc?')
            //             ->default(true)
            //             ->required(),
            //         Forms\Components\FileUpload::make('file')
            //             ->label('Excel fájl')
            //             ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
            //             ->required()
            //             ->disk(config('filesystems.default'))
            //             ->directory('imports')
            //             ->getUploadedFileNameForStorageUsing(
            //                 fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
            //                     ->prepend(substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 5)), 0, 5)),
            //             )
            //             ->visibility('private')
            //     ])
            //     ->action(function (array $data, array $arguments): void {
            //         $rules = array(
            //             'file' => 'required'
            //         );

            //         $validator = Validator::make($data, $rules);

            //         if ($validator->fails()) {
            //             Notification::make()
            //                 ->title('Hiba a feltöltés során!')
            //                 ->body('Excel fájl: ' . $validator->errors()->getMessages())
            //                 ->danger()
            //                 ->sendToDatabase(auth()->user());
            //         } else {
            //             $importer = new ImageImport([
            //                 'file'  => $data['file'],
            //                 'data'  => [
            //                     'header' => $data['header'],
            //                     'file'   => null
            //                 ]
            //             ]);
            //             /** Add authenticated user...
            //              */
            //             $importer->imported_by()->associate(auth()->user());
            //             $importer->save();

            //             ADOBImageImportBatch_new::dispatch($importer);

            //             Notification::make()
            //                 ->title('Importálás feltöltése sikerült!')
            //                 ->body('Az importálást beütemeztük az <a style="text-decoration: underline;" href="' . route('filament.neon-admin.resources.product-imports.index') . '">importálás oldalán</a> lesz elérhető.')
            //                 ->info()
            //                 ->actions([
            //                     Notifications\Actions\Action::make('view')
            //                         ->label('Megnyit')
            //                         ->button()
            //                         ->url(route('filament.neon-admin.resources.product-imports.index'))
            //                 ])
            //                 ->sendToDatabase(auth()->user());
            //         }
            //     }),
            // // ->slideOver(),

            Actions\CreateAction::make(),
        ];
    }
}
