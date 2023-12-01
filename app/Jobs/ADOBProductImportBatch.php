<?php

namespace App\Jobs;

use App\Imports\ADOBProductCollectionImport;
use App\Models\Columns\ADOBProductsImportColumns;
use App\Models\ProductImport;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;
use Excel;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Notifications\NovaNotification;
use Logtail\Monolog\LogtailHandler;
use Monolog\Logger;

class ADOBProductImportBatch implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $logger;

  public function __construct(
    protected ProductImport $import
  ) {


    $this->logger = new Logger('adob_importer');
    $this->logger->pushHandler(new LogtailHandler('1sKmnmxToqZ5NPAJy6EfvyAZ'));
  }

  /**
   * Get the middleware the job should pass through.
   *
   * @return array<int, object>
   */
  public function middleware(): array
  {
    return [new WithoutOverlapping($this->import->id)];
  }

  /**
   * Execute the job.
   */
  public function handle()
  {
    /**
     * @var ProductImport $this->import The Product Import model.
     */
    $_import = $this->import;

    /** List of jobs to dispatch...
     * @var array $batch_jobs
     */
    $batch_jobs = [];

    /** @var integer $record_counts
     */
    $records_counter = 0;

    if ($this->import->data['header']) {
      /** Getting the header. The loop will be able to run through right now.
       * @var array
       */
      $header = $this->import->data['file'][0];
    } else {
      $header = array_column(ADOBProductsImportColumns::cases(), 'value');
    }

    $this->logger->info('Import batch start! [' . $this->import->id . ']', [
      'import'  => $this->import->id
    ]);

    /** Import all possible brands.
     */
    $batch_jobs[] = new \App\Jobs\ADOBBrandImportJob($this->import->data['file'], $this->import->data['header'], \App\Models\Columns\ADOBProductsImportColumns::class, $this->import);

    $this->logger->info('Brand import added.', [
      'import'  => $this->import->id
    ]);

    // /** Import categories.
    //  */
    // $batch_jobs[] =  new \App\Jobs\ADOBCategoryImportJob($this->import->data['file'], $this->import->data['header'], \App\Models\Columns\ADOBProductsImportColumns::class, $this->import);

    // $this->logger->info('Categories import added.', [
    //   'import'  => $this->import->id
    // ]);

    // $batch_jobs[] = (new \App\Jobs\ADOBAllProductImportJob($this->import->data['file'], $this->import->data['header'], \App\Models\Columns\ADOBProductsImportColumns::class, $this->import));

    /** Separate bach for images.
     * @var array $import_images 
     */
    $import_images = [];

    /** Import products line-by-line. 
     */
    foreach ($this->import->data['file'] as $index => $row) {
      if ($row != $header && !empty($row[0])) { // Skip header or empty rows

        $batch_jobs[]     = (new \App\Jobs\ADOBProductImportJob(
          record: array_combine($header, $row),
          header: $header,
          columns: \App\Models\Columns\ADOBProductsImportColumns::class,
          import: $this->import,
          import_categories: true,
          import_images: false
        ));
        $import_images[]  = (new \App\Jobs\ADOBProductImportImagesJob(array_combine($header, $row), \App\Models\Columns\ADOBProductsImportColumns::class, $this->import));

        $this->logger->info('Product import added. (' . $row[0] . ') (Extended image handler)', [
          'import'  => $this->import->id
        ]);
        $records_counter++;
      }
    }

    $batch_jobs[] = new \App\Jobs\CountBrandCategoryProducts($_import);

    $batch_jobs[] = function () use ($_import) {
      /** Getting informaion from $batch
       */
      $_import->finished_at   = now();
      $_import->job           = 'Kész.';
      $_import->status        = $_import->fails_counter > 0 ? 'failed' : 'finished';
      $_import->save();


      Notification::make()
        ->title('Importálás folyamata...')
        ->body(($_import->fails_counter > 0) ? 'Végeztünk' : 'Sikeresen végeztünk!')
        ->success()
        ->sendToDatabase($_import->imported_by);
    };

    $batch_jobs[] = Bus::batch($import_images)
      ->then(function (Batch $batch) use ($_import) {
        $_import->job           = 'Kész.';
        $_import->save();

        Notification::make()
          ->title('Importálás folyamata...')
          ->body('Képek importálása sikeres!')
          ->success()
          ->sendToDatabase($_import->imported_by);
      })
      ->name('ADOB product import batch');

    Bus::chain($batch_jobs)
      ->catch(function (Throwable $e) use ($_import) {
        if (!$_import->finished_at)
        { //- We don't care about picture issues, so after it's finished, we became good girl and swallow all of our "fails".
          $_import->imported_by->notify(
            /** Uups...
             */
            Notification::make()
              ->title('Importálás folyamata...')
              ->body('Hiba: ' . $e->getMessage())
              ->danger()
              ->toDatabase()
          );
          $_import->fails_counter++;
          $_import->status = 'failed';
          $_import->job = 'Hiba: ' . $e->getMessage();
          $_import->save();
        }
      })
      ->onConnection('redis')
      ->onQueue('default')
      ->dispatch();

    // $this->import->batch_id = $batch->id;
    $this->import->records_counter = $records_counter;
    $this->import->status = 'running';
    $this->import->save();

    // return $batch->id;
  }
}
