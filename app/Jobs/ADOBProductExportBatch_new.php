<?php

namespace App\Jobs;

use App\Exports\ADOBProductCollectionExport;
use App\Exports\ADOBProductsExport_new;
use App\Models\Columns\ADOBProductsExportColumns;
use App\Models\ProductExport;
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

class ADOBProductExportBatch_new implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $logger;

  public function __construct(
    protected ProductExport $export
  ) {

    $this->logger = new Logger('adob_exporter');
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
      new WithoutOverlapping($this->export->id)
    ];
  }

  /**
   * Execute the job.
   */
  public function handle()
  {
      // Excel::export(new ADOBProductsExport_new($this->export->exported_by, $this->export), $this->export->file, null, \Maatwebsite\Excel\Excel::XLSX);
      (new ADOBProductsExport_new($this->export))->store($this->export->file); // we are using the trait exportable in the xxxExport which allow us to handle it from the controller directly
  }
}
