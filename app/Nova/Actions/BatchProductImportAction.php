<?php

namespace App\Nova\Actions;

// use App\Models\AccountData;

use App\Imports\ADOBProductCollectionImport;
use App\Jobs\ADOBProductImportBatch;
use App\Models\ProductImport;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\PendingBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Contracts\BatchableAction;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Excel;
use Laravel\Nova\Fields\Boolean;
use Throwable;

class BatchProductImportAction extends Action // implements BatchableAction, ShouldQueue
{
  public $name = 'Termékek importálása (ADOB)';
//  use Batchable, InteractsWithQueue, Queueable;

  // /**
  //  * Prepare the given batch for execution.
  //  *
  //  * @param  \Laravel\Nova\Fields\ActionFields  $fields
  //  * @param  \Illuminate\Bus\PendingBatch  $batch
  //  * @return void
  //  */
  // public function withBatch(ActionFields $fields, PendingBatch $batch)
  // {
  //   $batch->then(function (Batch $batch) {
  //     // All jobs completed successfully...

  //     $selectedModels = $batch->resourceIds;
  //   })->catch(function (Batch $batch, Throwable $e) {
  //     // First batch job failure detected...
  //   })->finally(function (Batch $batch) {
  //     // The batch has finished executing...
  //   });
  // }

  // TestQueuedAction

    /**
   * Perform the action on the given models.
   *
   * @param  \Laravel\Nova\Fields\ActionFields  $fields
   * @return mixed
   */
  public function handle(ActionFields $fields)
  {
    $rules = array(
      'file' => 'required|mimes:xls,xlsx'
    );

    $validator = Validator::make($fields->toArray(), $rules);

    if ($validator->fails()) {
      return Action::danger($validator->errors);
    } else {
      $file = $fields->file->storeAs('imports', $fields->file->getFilename().'_'.$fields->file->getClientOriginalName(), config('filesystems.default'));

      $importer = new ProductImport([
        'file'  => $file,
        'data'  => [
          'header' => $fields->header,
          'file'   => Excel::toArray(
            new ADOBProductCollectionImport(),
            $fields->file,
            null,
            \Maatwebsite\Excel\Excel::XLSX
          )[0], // Getting only the first sheet.
        ]
      ]);
      $importer->imported_by()->associate(request()->user());
      $importer->save();
      
      ADOBProductImportBatch::dispatch($importer);

      return Action::message('Az állomány feltöltve. A háttérben elindítjuk az importálás folyamatát...');
    }
  }

  /** Show popup window's fields.
   * 
   * @param NovaRequest $request
   * @return array of fields
   */
  public function fields(NovaRequest $request): array
  {
    return [
      File::make("File", 'file')
        ->rules('required', 'mimes:xls,xlsx'),
      Boolean::make('Fejléc van?', 'header')
        ->trueValue(1)
        ->falseValue(0)
        ->withMeta([
          'value' => 1
        ])
      // Number::make('Fejléc sor', 'header_row')
      //   ->min(0)
      //   ->step(1)
      //   ->withMeta([
      //     "value" => 1,
      //   ]),
    ];
  }
}
