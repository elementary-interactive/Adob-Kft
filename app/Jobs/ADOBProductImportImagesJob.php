<?php

namespace App\Jobs;

ini_set('memory_limit', '4000M');

use App\Models\Brand;
use App\Models\Category;
use App\Models\Columns\ADOBProductsImportColumns;
use App\Models\Product;
use App\Models\ProductImport;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
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

class ADOBProductImportImagesJob implements ShouldQueue
{
  use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $logger;

  /**
   * Create a new job instance.
   */
  public function __construct(
    protected array $record,
    protected string $columns, //- Columns enumeration
    protected ProductImport $import
  ) {
    //     use Monolog\Logger;
    // use Logtail\Monolog\LogtailHandler;

    $this->logger = new Logger('adob_importer');
    $this->logger->pushHandler(new LogtailHandler('1sKmnmxToqZ5NPAJy6EfvyAZ'));
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $this->import->job = 'Képek feldolgozása...';
    $this->import->save();
    
    $product = Product::where('product_id', $this->record[$this->columns::PRODUCT_ID->value])
      ->first();

    if ($product) {
      $this->handle_images($product);
    }
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

  public function record(): array
  {
    return (array) $this->record;
  }
  /** Parse Excel cells to discover images to store them for the products.
   * @return void
   */
  private function handle_images(Product $product): void
  {
    if (array_key_exists($this->columns::IMAGES->value, $this->record) && isset($this->record[$this->columns::IMAGES->value])) {
      // if (strpos($this->record[$this->columns::IMAGES->value], 'data:image/jpeg;base64,') == 0) { //- jpeg
      //   \File::put(storage_path().'/'.Str::random(6).'.jpg', base64_decode(str_replace('data:image/jpeg;base64,', '', $this->record[$this->columns::IMAGES->value])));
      // }

      $__images = explode(';', $this->record[$this->columns::IMAGES->value]);

      // dump($__images);

      $images   = array();
      $index    = 0;

      foreach ($__images as $string) {
        if (!array_key_exists($index, $images)) {
          $images[$index] = '';
        }
        $images[$index] .= $string;

        if (!str_contains($string, 'data:image/')) {
          $index++;
        }
      }

      // dump($images);
      if (!empty($images)) {
        foreach ($product->getMedia(Product::MEDIA_COLLECTION) as $media) {
          $media->delete();
        }

        foreach ($images as $string) {
          if (Str::startsWith($string, 'data:image/')) { //- base64 image
            $product
              ->addMediaFromBase64($string, ["image/jpeg", "image/png"])
              ->toMediaCollection(Product::MEDIA_COLLECTION);
          }

          if (Str::startsWith($string, 'http')) { //- http image
            $this->logger->info($this->record[$this->columns::PRODUCT_ID->value] . ' product image check: ' . $string, [
              'import'  =>  $this->import->id
            ]);

            try {
              $media = $product
                ->addMediaFromUrl($string)
                ->preservingOriginal()
                ->toMediaCollection(Product::MEDIA_COLLECTION);
              $media->save();
            } catch (\Exception $e) {
              // dump($e->getMessage());
              $this->logger->info($this->record[$this->columns::PRODUCT_ID->value] . ' product image error: ' . $string . ' (' . $e->getMessage() . ')', [
                'import'  =>  $this->import->id
              ]);
            }
          }
        }
      }
      // } else {
      //     throw new Exception($this->record[$this->columns::PRODUCT_ID->value].' nincs "'.$this->columns::IMAGES.'" oszlop!');
    }
  }
}
