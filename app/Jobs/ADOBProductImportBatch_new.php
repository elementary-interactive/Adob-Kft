<?php

namespace App\Jobs;

use App\Imports\ADOBProductsImport_new;
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
// use Excel;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Notifications\NovaNotification;
use Logtail\Monolog\LogtailHandler;
use Monolog\Logger;
use Maatwebsite\Excel\Facades\Excel;

class ADOBProductImportBatch_new implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $logger;

  public $batch;

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
    return [
      new WithoutOverlapping($this->import->id)
    ];
  }

  /**
   * Execute the job.
   */
  public function handle()
  {
      (new ADOBProductsImport_new($this->import, $this->logger))->import($this->import->file); // we are using the trait importable in the xxxImport which allow us to handle it from the controller directly
      // (new ADOBProductsImport_new($this->import, $this->logger, $this->batch))->import($this->import->file); // we are using the trait importable in the xxxImport which allow us to handle it from the controller directly

  }
}
