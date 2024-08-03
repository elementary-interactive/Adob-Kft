<?php

namespace App\Jobs;

use App\Imports\ADOBProductsImport_new;
use App\Models\Columns\ADOBProductsImportColumns;
use App\Models\ImageImport;
use App\Models\Product;
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

class ADOBImagesImportBatch_new implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $logger;

  public function __construct(
    protected ImageImport $import
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
    foreach ($this->import->images as $image)
    {

      if (strpos($image, '/') > 0)
      {
        list($path, $filename) = explode('/', $image);

        if (strpos($filename, '_') > 0)
        {
          list($product_id, $order) = explode('_', $filename);

          $product = Product::withoutGlobalScopes()
            ->where('product_id', '=', $product_id)
            ->first();

          if ($product)
          {
            $media = $product->addMedia(storage_path('app/'.$image))
              ->toMediaCollection(Product::MEDIA_COLLECTION);
            $media->order_column = substr($order, 0, strpos($order, '.'));
            $media->save();

            $this->import->job = $this->import->job.'<br/>'.$media->file_name.' elmentve '.$product->name.' ['.$media->order_column.']';
          }
        }
      }

      $this->import->records_handled = $this->import->records_handled + 1;
      $this->import->save();
    }
  }
}
