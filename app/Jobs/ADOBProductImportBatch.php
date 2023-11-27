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
    protected ProductImport $import)
  {
    

    $this->logger = new Logger('adob_importer');
    $this->logger->pushHandler(new LogtailHandler('1sKmnmxToqZ5NPAJy6EfvyAZ'));
  }

  /**
   * Execute the job.
   */
  public function handle()
  {
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
    }
    
    /** Import all possible brands.
     */
    $batch_jobs[] = (new \App\Jobs\ADOBBrandImportJob($this->import->data['file'], $this->import->data['header'], \App\Models\Columns\ADOBProductsImportColumns::class, $this->import));
    $this->logger->info('Brand import added.');

    /** Import categories.
     */
    $batch_jobs[] = (new \App\Jobs\ADOBCategoryImportJob($this->import->data['file'], $this->import->data['header'], \App\Models\Columns\ADOBProductsImportColumns::class, $this->import));
    $this->logger->info('Category import added.');

    // $batch_jobs[] = (new \App\Jobs\ADOBAllProductImportJob($this->import->data['file'], $this->import->data['header'], \App\Models\Columns\ADOBProductsImportColumns::class, $this->import));

    /** Import products line-by-line. 
     */
    foreach ($this->import->data['file'] as $index => $row) {
      if ($row != $header && !empty($row)) { // Skip header or empty rows
        $batch_jobs[] = (new \App\Jobs\ADOBProductImportJob(array_combine($header, $row), \App\Models\Columns\ADOBProductsImportColumns::class, $this->import));
        $this->logger->info('Product import added. ('.$row[0].')');
        $records_counter++;
      }
    }

    // $batch_jobs[] = (new \App\Jobs\CountBrandCategoryProducts());

    $_import = $this->import;

    $batch = Bus::batch($batch_jobs)
      ->then(function (Batch $batch) use ($_import) {
        $_import->status = 'running';
        $_import->save();
      })->catch(function (Batch $batch, Throwable $e) use ($_import) {
        $_import->imported_by->notify(
          // NovaNotification::make()
          //   ->message($e->getMessage())
          //   // ->action('Download', URL::remote('https://example.com/report.pdf'))
          //   ->icon('exclamation-circle')
          //   ->type('error')

          Notification::make()
            ->title('Importálás folyamata...')
            ->body('Hiba: '.$e->getMessage())
            ->danger()
            ->toDatabase()
        );
        $_import->fails_counter = $batch->failedJobs;
        $_import->status = 'failed';
        $_import->save();
      })->finally(function (Batch $batch) use ($_import)
      {
        $_import->fails_counter = $batch->failedJobs;
        $_import->finished_at = $batch->finishedAt;
        $_import->fails_counter = $batch->failedJobs;
        $_import->status = $batch->failedJobs > 0 ? 'failed' : 'finished';
        $_import->save();
        
        CountBrandCategoryProducts::dispatch();
        $_import->imported_by->notify(
          Notification::make()
              ->title('Importálás folyamata...')
              ->body('Sikeresen végeztünk!')
              ->success()
              ->toDatabase()
        );
      })
        ->name('ADOB product import batch')
        ->allowFailures(true)
        // ->onConnection('database')
        ->dispatch();

    $this->import->batch_id = $batch->id;
    $this->import->records_counter = $records_counter;
    $this->import->status = 'running';
    $this->import->save();

    return $batch->id;
  }
}
