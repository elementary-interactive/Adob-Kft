<?php

namespace App\Jobs;

use App\Imports\ADOBProductCollectionImport;
use App\Models\ProductImport;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;
use Excel;
class ADOBProductImportBatch implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public function __construct(
    protected ProductImport $import)
  {
    
  }

  /**
   * Execute the job.
   */
  public function handle()
  {
    dump($this);

    // $items = collect([
    //   ['id' => 'product_id_1', 'name' => 'XY1', 'prop1' => 'val1'],
    //   ['id' => 'product_id_2', 'name' => 'XY2', 'prop2' => 'val2'],
    //   ['id' => 'product_id_3', 'name' => 'XY3', 'prop3' => 'val3'],
    //   ['id' => 'product_id_4', 'name' => 'XY4', 'prop4' => 'val4'],
    //   ['id' => 'product_id_5', 'name' => 'XY5', 'prop5' => 'val5'],
    //   ['id' => 'product_id_6', 'name' => 'XY6', 'prop6' => 'val6'],
    //   ['id' => 'product_id_7', 'name' => 'XY7', 'prop7' => 'val7'],
    //   ['id' => 'product_id_8', 'name' => 'XY8', 'prop8' => 'val8'],
    //   ['id' => 'product_id_9', 'name' => 'XY9', 'prop9' => 'val9'],
    //   ['id' => 'product_id_10', 'name' => 'XY10', 'prop10' => 'val10'],
    // ]);

    dump(storage_path($this->import->file));
    
    $items = Excel::toArray(
      new ADOBProductCollectionImport(),
      storage_path($this->import->file),
      null,
      \Maatwebsite\Excel\Excel::XLSX
    );

    dump($items);

    foreach ($items as $row) {
      $batch_jobs[] = (new \App\Jobs\ADOBProductImportJob($row))->delay(Carbon::now()->addSeconds(90));
    }

    $batch = Bus::batch($batch_jobs)
      ->then(function (Batch $batch) {
        // All jobs completed successfully...
      })->catch(function (Batch $batch, Throwable $e) {
        // First batch job failure detected...
      })->finally(function (Batch $batch) {
        // The batch has finished executing...
      })
        ->name('ADOB product import batch')
        ->dispatch();

    return $batch->id;
  }
}
