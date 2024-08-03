<?php

namespace App\Jobs;

// /**
//  * Whhoooo!!! Be careful!!!
//  **/
// ini_set('max_execution_time', 1200);
// ini_set('memory_limit', '4000M');
// set_time_limit(1200);

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImport;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Logtail\Monolog\LogtailHandler;
use Monolog\Logger;
use Neon\Models\Statuses\BasicStatus;

class ADOBProductImportImagesJob implements ShouldQueue //, ShouldBeUnique
{
  use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  const MAX_SUB_CATEGORY_COUNT    = 5;

  static $columns = \App\Models\Columns\ADOBProductsImportColumns::class;

  private $logger;

  // public $timeout = 1200;

  /**
   * Create a new job instance.
   */
  public function __construct(
    private Product $product,
    private array $row
  ) {
    $this->logger = new Logger('adob_importer');
    $this->logger->pushHandler(new LogtailHandler('1sKmnmxToqZ5NPAJy6EfvyAZ'));
  }

  // /**
  //  * Get the middleware the job should pass through.
  //  *
  //  * @return array<int, object>
  //  */
  // public function middleware(): array
  // {
  //   return [new WithoutOverlapping('category')];
  // }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    if (array_key_exists(self::$columns::IMAGES->value, $this->row) && isset($this->row[self::$columns::IMAGES->value])) {

      $image_sources = explode(';', $this->row[self::$columns::IMAGES->value]);
      
      $images   = [];
      $index    = 0;

      foreach ($image_sources as $string) {
        if (!array_key_exists($index, $images)) {
          $images[$index] = '';
        }
        $images[$index] .= $string;

        if (!str_contains($string, 'data:image/')) {
          $index++;
        }
      }

      foreach ($images as $string) {
        if (Str::startsWith($string, 'data:image/')) { //- base64 image
          $this->product
            ->addMediaFromBase64($string, ["image/jpeg", "image/png"])
            ->toMediaCollection(Product::MEDIA_COLLECTION);
        }

        if (Str::startsWith($string, 'http')) { //- http image
          $media = $this->product
            ->addMediaFromUrl($string)
            ->preservingOriginal()
            ->toMediaCollection(Product::MEDIA_COLLECTION);
          $media->save();
        }
      }
    }
  }
}
