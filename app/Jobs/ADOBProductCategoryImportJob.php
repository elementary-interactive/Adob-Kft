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

class ADOBProductCategoryImportJob implements ShouldQueue//, ShouldBeUnique
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
    private array $row,
    private ProductImport $tracker
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
    // return [new WithoutOverlapping('category')];
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $columns  = array_keys($this->row);

    for ($categories_index = 1; $categories_index <= 3; $categories_index++)
    {
      $main_category_column = Arr::first(preg_grep(($categories_index > 1) ? "/" . self::$columns::MAIN_CATEGORY->value . "[^\d]*{$categories_index}[^\w]*/" : "/" . self::$columns::MAIN_CATEGORY->value . "/", $columns));

      if (array_key_exists($main_category_column, $this->row) && $this->row[$main_category_column]) {
        // echo ("oszlop létezik.\n\r");
        $main_category = Category::firstOrCreate([
          'slug'        => Str::slug($this->row[$main_category_column]),
          'parent_id'   => null,
        ], [
          'name'        => $this->row[$main_category_column],
          'description' => $this->row[$main_category_column]
        ]);

        $category = null;
        // dump($main_category);
        for ($sub_category_count = 1; $sub_category_count <= self::MAX_SUB_CATEGORY_COUNT; $sub_category_count++) {
          if (is_null($category)) {
            $category = $main_category;
          }
          $sub_category_column = Arr::first(preg_grep(($categories_index > 1) ? "/" . self::$columns::SUB_CATEGORY->value . "{$sub_category_count}[^\d]*{$categories_index}[^\w]*/" : "/" . self::$columns::SUB_CATEGORY->value . "{$sub_category_count}/", $columns));

          if (isset($this->row[$sub_category_column]) && !is_null($this->row[$sub_category_column]))
          {
            $sub_category = Category::firstOrNew([
              'slug'        => Str::slug($this->row[$sub_category_column]),
              'parent_id'   => $category->id,
            ], [
              'name'        => $this->row[$sub_category_column]
            ]);

            if (!$sub_category->exists) {
              $this->tracker->increaseCategoryInserted();
              
              $sub_category->save();
              $sub_category->makeChildOf($category);
            }

            $category = $sub_category;
          }
        }

        $this->product->categories()->attach($category, [
          'is_main' => ($categories_index == 1),
          'order'   => 0,
        ]);
      }
    }
  }
}
