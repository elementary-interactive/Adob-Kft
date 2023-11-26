<?php

namespace App\Filament\Resources\ProductResource\Pages;

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
                    ->preserveFilenames()
            ])
            ->action(function (array $data, array $arguments): void {
                $author = auth()->user();

                $rules = array(
                    'file' => 'required|mimes:xls,xlsx'
                  );
              
                  $validator = Validator::make($data, $rules);
              
                  if ($validator->fails()) {
                    dump($validator->errors());
                    foreach ($validator->errors() as $error)
                    {
                        dd($error);
                        Notification::make()
                            ->title('Hiba a feltöltés során!')
                            ->body('Excel fájl: '.$error)
                            ->danger()
                            ->toBroadcast($author);
                    }
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
                    $importer->imported_by()->associate(request()->user());
                    $importer->save();
                    
                    ADOBProductImportBatch::dispatch($importer);
              
                    Notification::make()
                        ->title('Feltöltés sikerült!')
                        ->body('Az importálást beütemeztük az <a href="#">oldalon</a> lesz elérhető.')
                        // ->toDatabase()
                        ->success()
                        ->toBroadcast($author);
                  }
            }),
            // ->slideOver(),
        ];
    }
}
