<?php

namespace App\Filament\Resources\ProductResource\Pages;

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
                ->icon('heroicon-o-arrow-up-tray')
                ->modalIcon('heroicon-o-arrow-up-tray')
                ->form([
                    Forms\Components\Toggle::make('header')
                        ->label('Fejléc?')
                        ->default(true)
                        ->required(),
                    Forms\Components\FileUpload::make('file')
                        ->label('Excel fájl')
                        ->required()
                        ->disk(config('filesystems.default'))
                        ->directory('imports')
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
                                    ->url(route('filament.admin2.resources.product-imports.index'), shouldOpenInNewTab: true)
                            ])
                            ->sendToDatabase(auth()->user());
                    }
                }),
            // ->slideOver(),
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
