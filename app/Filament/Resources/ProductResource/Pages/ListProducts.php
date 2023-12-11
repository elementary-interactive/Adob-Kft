<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Exports\ADOBProductsExport;
use App\Filament\Resources\ProductImportResource\Widgets\ProductImportOverview;
use App\Filament\Resources\ProductResource;
use App\Imports\ADOBProductCollectionImport;
use App\Jobs\ADOBProductImportBatch;
use App\Models\ProductImport;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Validator;
use Excel;
use Filament\Notifications;
use Filament\Panel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('ADOB_batch_import')
                ->label('Importálás')
                ->modalHeading('Importálás')
                ->modalDescription('ADOB Excel file importálása.')
                ->modalSubmitActionLabel('Importálás')
                ->icon('heroicon-o-arrow-down-tray')
                ->modalIcon('heroicon-o-arrow-down-tray')
                ->form([
                    Forms\Components\Toggle::make('header')
                        ->label('Fejléc?')
                        ->default(true)
                        ->required(),
                    Forms\Components\FileUpload::make('file')
                        ->label('Excel fájl')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->required()
                        ->disk(config('filesystems.default'))
                        ->directory('imports')
                        ->getUploadedFileNameForStorageUsing(
                            fn (TemporaryUploadedFile $file): string => (string) str($file->getClientOriginalName())
                                ->prepend(substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 5)), 0, 5)),
                        )
                        ->visibility('private')
                ])
                ->action(function (array $data, array $arguments): void {
                    $rules = array(
                        'file' => 'required'
                    );

                    $validator = Validator::make($data, $rules);

                    if ($validator->fails()) {
                        Notification::make()
                            ->title('Hiba a feltöltés során!')
                            ->body('Excel fájl: ' . $validator->errors()->getMessages())
                            ->danger()
                            ->sendToDatabase(auth()->user());
                    } else {
                        // $file = $fields->file->storeAs('imports', $fields->file->getFilename().'_'.$fields->file->getClientOriginalName(), config('filesystems.default'));

                        $importer = new ProductImport([
                            'file'  => $data['file'],
                            'data'  => [
                                'header' => $data['header'],
                                'file'   => Excel::toArray(
                                    new ADOBProductCollectionImport(),
                                    $data['file'],
                                    null,
                                    \Maatwebsite\Excel\Excel::XLSX
                                )[0], // Getting only the first sheet.
                            ]
                        ]);
                        $importer->imported_by()->associate(auth()->user());
                        $importer->save();

                        ADOBProductImportBatch::dispatch($importer);

                        Notification::make()
                            ->title('Importálás feltöltése sikerült!')
                            ->body('Az importálást beütemeztük az <a style="text-decoration: underline;" href="'.route('filament.admin2.resources.product-imports.index').'">importálás oldalán</a> lesz elérhető.')
                            ->info()
                            ->actions([
                                Notifications\Actions\Action::make('view')
                                    ->label('Megnyit')
                                    ->button()
                                    ->url(route('filament.admin2.resources.product-imports.index'))
                            ])
                            ->sendToDatabase(auth()->user());
                    }
                }),
                // ->slideOver(),
            Actions\Action::make('ADOB_batch_export')
                ->label('Exportálás')
                ->icon('heroicon-o-arrow-up-tray')
                ->action(function () {

                    /** The name of the file to export data into...
                     * @var string
                     */
                    $file = 'ADOB_termek-export-' . date('Y-m-d H_i_s') . '.xlsx';

                    $response = Excel::store((new ADOBProductsExport(auth()->user(), null)), 'exports/'.$file, config('filesystem.default', 'local'), \Maatwebsite\Excel\Excel::XLSX);

                    if (!$response) {//- instanceof BinaryFileResponse || $response->isInvalid()) {
                        Notification::make()
                            ->title('Exportálás sikertelen')
                            ->warning()
                            ->sendToDatabase(auth()->user());
                    //   return Action::danger(__('Resource could not be exported.'));
                    } else {
                        Notification::make()
                            ->title('Exportálás sikerült!')
                            ->body('Az exportálás elkészült, az alábbi gombra kattintva letölthető!')
                            ->success()
                            ->actions([
                                Notifications\Actions\Action::make('view')
                                    ->label('Megnyit')
                                    ->button()
                                    ->url(URL::temporarySignedRoute('export.download', now()->addDays(7), [
                                        'path'     => encrypt('exports/'.$file),
                                        'filename' => encrypt($file),
                                    ]))
                            ])
                            ->sendToDatabase(auth()->user());
                            return redirect()->to(URL::temporarySignedRoute('export.download', now()->addDays(7), [
                                'path'     => encrypt('exports/'.$file),
                                'filename' => encrypt($file),
                            ]));
                    }
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // ProductImportOverview::class
        ];
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s');
    }
}
