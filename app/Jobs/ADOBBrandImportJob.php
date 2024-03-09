<?php

namespace App\Jobs;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImport;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Logtail\Monolog\LogtailHandler;
use Monolog\Logger;
use Neon\Models\Statuses\BasicStatus;

class ADOBBrandImportJob implements ShouldQueue, ShouldBeUnique
{
  use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $logger;

  /**
   * Create a new job instance.
   */
  public function __construct(
    protected $records,
    protected $records_has_header = true,
    protected $columns,
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
  public function handle(): void
  {
    $this->import->job = 'Márkák importálása...';
    $this->import->save();
    
    $this->save_brand();
  }

  /**
   * @return void
   */
  private function save_brand(): void
  {
    /** Get the header...
     */
    if ($this->records_has_header) {
      $header = $this->import->data['file'][0];
    }

    foreach ($this->records as $record_data) {
      if ($record_data != $header) {
        /**  
         * @var array $record Associative array of the given record.
         */
        $record = array_combine($header, $record_data);

        if ($record[$this->columns::PRODUCT_ID->value] && $record[$this->columns::BRAND->value]) {
          /** 
           * @var Brand $brand The product's brand.
           */
          $brand = Brand::firstOrNew([
            'slug'        => Str::slug($record[$this->columns::BRAND->value]),
          ], [ //- Fill up data.
            'name'        => $record[$this->columns::BRAND->value],
            'is_featured' => false
          ]);

          if (!$brand->exists) {
            $this->import->increaseBrandInserted();
            $brand->save();

            /** Logging... */
            $this->logger->info('Brand import done. [' . $record[$this->columns::BRAND->value] . ']', [
              'import'  =>  $this->import->id,
              'record'  => $record
            ]);
          }
        }
      }
    }

    Notification::make()
      ->title('Importálás folyamata...')
      ->body('Márkák sikeresen importálva')
      ->info()
      ->sendToDatabase($this->import->imported_by ?: auth()->user());
  }
}
