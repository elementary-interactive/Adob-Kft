<?php

namespace App\Jobs;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImport;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
 use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
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

class ADOBBrandImportJob implements ShouldQueue
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
   * Execute the job.
   */
  public function handle(): void
  {
    $this->save_brand();
  }

  /**
   * @return void
   */
  private function save_brand(): void
  {
    /** Get the header...
     */
    if ($this->records_has_header)
    {
      $header = $this->import->data['file'][0];
    }

    foreach ($this->records as $record_data)
    {
      if ($record_data != $header)
      {
        /**  
         * @var array $record Associative array of the given record.
         */
        $record = array_combine($header, $record_data);

        $this->logger->info('Brand found: '.$record[$this->columns::BRAND->value].' at '.$this->columns::BRAND->value);
        
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

          $this->logger->info('Brand imported: '.$record[$this->columns::BRAND->value]);
        }
      }
    }
  }

}
