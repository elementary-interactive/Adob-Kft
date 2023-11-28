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

class ADOBCategoryImportJob implements ShouldQueue, ShouldBeUnique
{
  use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  const MAX_SUB_CATEGORY_COUNT    = 5;

  private $logger;

  // public $timeout = 800;

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
    $this->save_categories();
  }

  private function save_categories()
  {
    $result = [];
    /** Get the header...
     */
    if ($this->records_has_header) {
      $header = $this->import->data['file'][0];
    }

    foreach ($this->records as $record_index => $record_data) {
      if ($record_data != $header) {
        /**  
         * @var array $record Associative array of the given record.
         */
        $record = array_combine($header, $record_data);

        if (isset($record[$this->columns::PRODUCT_ID->value]) && $record[$this->columns::PRODUCT_ID->value]) {

          for ($categories_index = 1; $categories_index <= 3; $categories_index++) {
            $main_category_column = Arr::first(preg_grep(($categories_index > 1) ? "/" . $this->columns::MAIN_CATEGORY->value . "[^\d]*{$categories_index}[^\w]*/" : "/" . $this->columns::MAIN_CATEGORY->value . "/", $header));

            if ($record[$main_category_column]) {
              $category = Category::firstOrCreate([
                'slug'        => Str::slug($record[$main_category_column]),
                'parent_id'   => null
              ], [
                'name'        => $record[$main_category_column],
                'description' => $record[$main_category_column]
              ]);

              // $category = null;

              for ($sub_category_count = 1; $sub_category_count <= self::MAX_SUB_CATEGORY_COUNT; $sub_category_count++) {
                // if (is_null($category))
                // {
                //   $category = $main_category;
                // }
                $sub_category_column = Arr::first(preg_grep(($categories_index > 1) ? "/" . $this->columns::SUB_CATEGORY->value . "{$sub_category_count}[^\d]*{$categories_index}[^\w]*/" : "/" . $this->columns::SUB_CATEGORY->value . "{$sub_category_count}/", $header));

                if (isset($record[$sub_category_column]) && !is_null($record[$sub_category_column])) {
                  $sub_category = Category::firstOrNew([
                    'slug'        => Str::slug($record[$sub_category_column]),
                    'parent_id'   => $category->id
                  ], [
                    'name'        => $record[$sub_category_column]
                  ]);

                  if (!$sub_category->exists) {
                    $this->import->increaseCategoryInserted();
                    $sub_category->save();
                    $sub_category->makeChildOf($category);
                  } else {
                    $this->import->increaseCategoryModified();
                  }

                  $category = $sub_category;
                }
              }
              $result[$categories_index] = $category->id;
            }
          }
          /** Save data into a separated part of the import data....
           */
          $this->import->addCategoryIds($record[$this->columns::PRODUCT_ID->value], $result);

          /** Logging... */
          $this->logger->info('Category import done. ['.implode(', ', $result).']', [
            'import'  =>  $this->import->id,
            'record'  => $record
          ]);
        }
      }
    }

    /** Notify importer. */
    Notification::make()
      ->title('Importálás folyamata...')
      ->body('Kategóriák sikeresen importálva')
      ->info()
      ->sendToDatabase($this->import->imported_by);
  }
}
