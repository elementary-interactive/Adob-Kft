<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Columns;
use App\Models\Product;
use App\Models\ProductImport;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Neon\Admin\Models\Admin;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\PersistRelations;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Neon\Models\Statuses\BasicStatus;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\BeforeImport;

class ADOBProductsImport_new implements ToModel, WithUpserts, PersistRelations, WithBatchInserts, ShouldQueue, WithEvents, WithHeadingRow, WithChunkReading, WithValidation
{
  use Importable, RemembersRowNumber, SkipsErrors, SkipsFailures;

  const HEADING_ROW = 1;

  const MAX_SUB_CATEGORY_COUNT    = 5;

  static $columns = \App\Models\Columns\ADOBProductsImportColumns::class;

  /** @var User */
  public $imported_by;

  public $headerRow;

  /** @var ProductImport */
  public $tracker;

  /** @var int */
  private $rows = 0;

  /** @var int */
  private $rows_insserted = 0;


  public function __construct(ProductImport $tracker)
  {
    /** The importer user. who need to set up for notifications...
     * @var Admin
     */
    $this->imported_by  = $tracker->imported_by;

    /** The tracker for counts.
     *
     */
    $this->tracker      = $tracker;
  }

  public function registerEvents(): array
  {
    return [
      BeforeImport::class => function (BeforeImport $event)
      {
        $totalRows = $event->getReader()->getTotalRows();
        $this->tracker->records_counter = $totalRows[array_key_first($totalRows)] - 1; // Because of the header...

        Notification::make()
          ->title('Importálás folyamata...')
          ->body($this->tracker->records_counter.'db rekord importálása...')
          ->info()
          ->sendToDatabase($this->tracker->imported_by);
      },

      ImportFailed::class => function (ImportFailed $event)
      {
        $failures = $event->getException()->failures();

        foreach ($failures as $fail)
        {
          $this->error($fail->row().' "'.$fail->attribute().'": '.implode(', ', $fail->errors()));
        }
      },

      AfterImport::class => function(AfterImport $event)
      {
        $this->tracker->status = 'finished';
        $this->tracker->products_inserted = $this->rows_insserted;
        $this->tracker->finished_at = now();
        $this->tracker->save();

        Notification::make()
          ->title('Importálás folyamata...')
          ->body((($this->tracker->fails_counter > 0) ? 'Végeztünk.' : 'Sikeresen végeztünk!').' A termékek mostmár elérhetők a weben. A képek importálása még folyamatban.')
          ->success()
          ->sendToDatabase($this->tracker->imported_by);
      }
    ];
  }

  /** Handle heading row.
   *
   * @see https://docs.laravel-excel.com/3.1/imports/heading-row.html
   *
   * @return int Row's index number.
   */
  public function headingRow(): int
  {
    return self::HEADING_ROW;
  }

  /**
   * @return string|array
   */
  public function uniqueBy()
  {
    return 'product_id';
  }

  public function batchSize(): int
  {
    return 1;
  }

  /**
   * @see https://docs.laravel-excel.com/3.1/imports/chunk-reading.html
   *
   */
  public function chunkSize(): int
  {
    return 1000;
  }

  public function model(array $row)
  {
    $this->rows++;

    $this->tracker->status = 'running';
    $this->tracker->save();

    $result = null;

    // dd($row);

    try {
      if (self::to_save($row)) {
        $result = $this->save_product($row, self::is_active($row));
      }

      if (self::to_delete($row)) {
        $result = $this->delete_product($row);
      }
    } catch (\Exception $e) {
      // $this->error($this->rows.'. - '.$e->getMessage());
    } catch (\Throwable $e) {
      // $this->error($this->rows.'. - '.$e->getMessage());
    }

    // $result->setRelation('team', new Team(['name' => $row[1]]));

    return $result;
  }

  /**
   * @return array
   */
  public function rules(): array
  {
    return [
      self::$columns::PRODUCT_ID->value               => 'required', //|unique:products,product_id',
      self::$columns::PRODUCT_NAME->value             => '',
      self::$columns::BRAND->value                    => '',
      self::$columns::PRICE->value                    => '',
      self::$columns::DESCRIPTION->value              => '',
      self::$columns::PACKAGING->value                => '',
      self::$columns::EAN->value                      => 'numeric|nullable',
      self::$columns::PRODUCT_NUMBER->value           => '',
      self::$columns::ON_SALE->value                  => '',
      self::$columns::MAIN_CATEGORY->value            => 'required_unless:'.self::$columns::COMMAND->value.',"d"|nullable',
      self::$columns::COMMAND->value                  => '',
      self::$columns::SUB_CATEGORY->value             => '',
    ];
  }

  /**
   * @return array
   */
  public function customValidationMessages()
  {
    return [
      self::$columns::PRODUCT_ID->value . '.required'     => 'A termék azonosító megadása kötelező!',
      // self::$columns::PRODUCT_ID->value . '.unique'       => 'A termék azonosító egyedi kell legyen!',
      self::$columns::EAN->value . 'numeric'              => 'Az EAN szám csak szám lehet.',
      self::$columns::MAIN_CATEGORY->value . '.required'  => 'A fő kategória megadása kötelező!',
    ];
  }

  private function error(string $message, string $icon = 'exclamation-circle')
  {
    $this->tracker->addFail($message);
    $this->tracker->status = 'failed';
    $this->tracker->save();

    Notification::make()
      ->title('Importálás folyamata...')
      ->body($message)
      ->danger()
      ->sendToDatabase($this->imported_by);
  }

  /**
   * @param $row
   * @param bool $is_aggregate
   * @param null $is_active
   * @return Product
   */
  private function save_product($row, $is_active = null): Product
  {
    $is_new = null;

    $product = Product::firstOrNew([
      'product_id' => $row[self::$columns::PRODUCT_ID->value]
    ]);

    if (array_key_exists(self::$columns::PRODUCT_NAME->value, $row) && $row[self::$columns::PRODUCT_NAME->value]) {
      $product->name            = $row[self::$columns::PRODUCT_NAME->value];
      $product->slug            = Str::slug($row[self::$columns::PRODUCT_NAME->value], '-').'_'.Str::slug($row[self::$columns::PRODUCT_ID]);
    }

    if (array_key_exists(self::$columns::PACKAGING->value, $row)) {
      $product->packaging       = $row[self::$columns::PACKAGING->value];
    }
    if (array_key_exists(self::$columns::DESCRIPTION->value, $row)) {
      $product->description     = nl2br(mb_convert_encoding($row[self::$columns::DESCRIPTION->value], 'UTF-8'));
    }
    if (array_key_exists(self::$columns::EAN->value, $row) && is_numeric($row[self::$columns::EAN->value])) {
      $product->ean             = $row[self::$columns::EAN->value];
    }
    if (array_key_exists(self::$columns::PRODUCT_NUMBER->value, $row)) {
      $product->product_number  = $row[self::$columns::PRODUCT_NUMBER->value];
    }
    if (array_key_exists(self::$columns::PRICE->value, $row)) {
      $product->price           = $row[self::$columns::PRICE->value];
    }

    $product->on_sale         = (array_key_exists(self::$columns::ON_SALE->value, $row) && strtolower($row[self::$columns::ON_SALE->value]) === 'y');
    $product->status          = ($is_active) ? BasicStatus::Active->value : BasicStatus::Inactive->value;

    if (array_key_exists(self::$columns::BRAND->value, $row) && isset($row[self::$columns::BRAND->value])) {
      /**
       * @var Brand $brand The product's brand.
       */
      $brand = Brand::firstOrNew([
          'slug'        => Str::slug($row[self::$columns::BRAND->value])
        ],[
          'name'        => $row[self::$columns::BRAND->value],
          'status'      => BasicStatus::Active->value
        ]);

      if (!$brand->exists) {
        $this->tracker->increaseBrandInserted();
        $brand->save();
      }

      // Connect brand to product.
      $product->brand()->associate($brand);
    }

    if ($product->exists) {
      $this->tracker->increaseProductModified();

      /** Detach from all categories, will re-attach new ones.s
       */
      $product->categories()->detach();
    } else {
      // $this->tracker->increaseProductInserted();
      $this->rows_insserted++;
    }
    $product->save();

    /** Check is there category & adding to categories.
     *
     * This method will also insert or modify categories.
     */
    $this->attach_categories($product, $row);

    /** Remove all images from the product.
     * If user added new pictures, that will be executed after this so this way user can replace all the images.
     */
    if (array_key_exists(self::$columns::IMAGES_DELETE->value, $row) && $row[self::$columns::IMAGES_DELETE->value] == 'y')
    {
      $this->delete_images($product, $row);
    }

    /** Store images to the product.
     */
    $this->save_images($product, $row);

    return $product;
  }

  /**
   * @param $row
   *
   * @return Product
   */
  private function delete_product($row)
  {
    $product = Product::where('product_id', '=', $row[self::$columns::PRODUCT_ID->value])->first();
    $product->delete();

    return $product;
  }

  private function delete_images($product, $row)
  {
    $product->clearMediaCollection(Product::MEDIA_COLLECTION);
    $product->clearMediaCollection(Product::MEDIA_MAIN);
  }

  private function save_images($product, $row)
  {
    if (array_key_exists(self::$columns::IMAGES->value, $row) && isset($row[self::$columns::IMAGES->value])) {
      // if (strpos($row[self::$columns::IMAGES->value], 'data:image/jpeg;base64,') == 0) { //- jpeg
      //   \File::put(storage_path().'/'.Str::random(6).'.jpg', base64_decode(str_replace('data:image/jpeg;base64,', '', $row[self::$columns::IMAGES->value])));
      // }

      $__images = explode(';', $row[self::$columns::IMAGES->value]);
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
      foreach ($images as $string) {
        // dump($string);
        if (Str::startsWith($string, 'data:image/')) { //- base64 image
          $product
            ->addMediaFromBase64($string, ["image/jpeg", "image/png"])
            ->toMediaCollection(Product::MEDIA_COLLECTION);
        }

        if (Str::startsWith($string, 'http')) { //- http image
          // dump('http image');
          $media = $product
            ->addMediaFromUrl($string)
            ->preservingOriginal()
            ->toMediaCollection(Product::MEDIA_COLLECTION);
          $media->save();
          // dump($product, $media);
        }
      }
    }
  }

  /** Parse and save categories. Returns with nodes where to product should be
   * attached.
   *
   * @param Product $product
   * @param array $row The row data.
   *
   * @return array $categories
   */
  private function attach_categories(Product $product, array $row)
  {
    $columns  = array_keys($row);

    for ($categories_index = 1; $categories_index <= 3; $categories_index++)
    {
      $main_category_column = Arr::first(preg_grep(($categories_index > 1) ? "/" . self::$columns::MAIN_CATEGORY->value . "[^\d]*{$categories_index}[^\w]*/" : "/" . self::$columns::MAIN_CATEGORY->value . "/", $columns));

      if ($row[$main_category_column]) {
        $main_category = Category::firstOrCreate([
          'slug'        => Str::slug($row[$main_category_column]),
          'parent_id'   => null,
        ], [
          'name'        => $row[$main_category_column],
          'description' => $row[$main_category_column]
        ]);

        $category = null;

        for ($sub_category_count = 1; $sub_category_count <= self::MAX_SUB_CATEGORY_COUNT; $sub_category_count++) {
          if (is_null($category)) {
            $category = $main_category;
          }
          $sub_category_column = Arr::first(preg_grep(($categories_index > 1) ? "/" . self::$columns::SUB_CATEGORY->value . "{$sub_category_count}[^\d]*{$categories_index}[^\w]*/" : "/" . self::$columns::SUB_CATEGORY->value . "{$sub_category_count}/", $columns));

          if (isset($row[$sub_category_column]) && !is_null($row[$sub_category_column]))
          {
            $sub_category = Category::firstOrNew([
              'slug'        => Str::slug($row[$sub_category_column]),
              'parent_id'   => $category->id,
            ], [
              'name'        => $row[$sub_category_column]
            ]);

            if (!$sub_category->exists) {
              $this->tracker->increaseCategoryInserted();
              $sub_category->save();
              $sub_category->makeChildOf($category);
            }

            $category = $sub_category;
          }
        }

        $product->categories()->attach($category, [
          'is_main' => ($categories_index == 1),
          'order'   => 0,
        ]);
      }
    }
  }

  public static function to_save(array $row): bool
  {
    return (strtolower($row[self::$columns::COMMAND->value]) === 'y' || strtolower($row[self::$columns::COMMAND->value]) === 'i');
  }

  public static function is_active(array $row): bool
  {
    return (strtolower($row[self::$columns::COMMAND->value]) === 'y');
  }

  public static function to_delete(array $row): bool
  {
    return (strtolower($row[self::$columns::COMMAND->value]) === 'd');
  }
}
