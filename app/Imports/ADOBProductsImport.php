<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Columns;
use App\Models\ProductImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Neon\Admin\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

use Illuminate\Validation\Rule;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\URL;
use Maatwebsite\Excel\Concerns\WithEvents;
use Neon\Models\Statuses\BasicStatus;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Events\AfterImport;


class ADOBProductsImport implements ToModel, WithValidation, WithHeadingRow, WithChunkReading, WithEvents, ShouldQueue
{
  use Importable;

  const HEADING_ROW = 1;

  const MAX_SUB_CATEGORY_COUNT    = 5;

  static $columns = \App\Models\Columns\ProductImportColumns::class;

  public $imported_by;

  public $headerRow;

  public $tracker;

  public function __construct(Admin $imported_by, $tracker)
  {
    /** The importer user. who need to set up for notifications...
     * @var Admin
     */
    $this->imported_by  = $imported_by;
    
    /** The tracker for counts.
     *
     */
    $this->tracker      = $tracker;
  }

  // public function __destruct()
  // {
  //   $this->tracker->save();
  // }

  public function registerEvents(): array
  {
    $me = $this;

    return [
      ImportFailed::class => function (ImportFailed $event) use ($me) {
        $me->tracker->addFail($event->getException()->getMessage());
        $me->tracker->status = 'failed';
        $me->tracker->save();

        $me->error($event->getException()->getMessage());
      },
      AfterImport::class => function(AfterImport $event) use ($me)
      {
        $me->tracker->status = 'finished';
        $me->tracker->finished_at = now();
        $me->tracker->save();

        $me->imported_by->notify(
          NovaNotification::make()
            ->message('Importálás vége!')
            // ->action('Download', URL::remote('https://example.com/report.pdf'))
            ->icon('check-circle')
            ->type('success')
        );
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
   * @see https://docs.laravel-excel.com/3.1/imports/chunk-reading.html
   * 
   */
  public function chunkSize(): int
  {
    return 10;
  }

  public function batchSize(): int
  {
    return 10;
  }

  public function model(array $row)
  {
    $this->tracker->status = 'running';
    $this->tracker->save();

    try {
      if (self::to_save($row)) {
        $result = $this->save_product($row, self::is_active($row));
      }

      if (self::to_delete($row)) {
        $result = $this->delete_product($row);
      }
    } catch (\Exception $e) {
      $this->error($e->getMessage());
    } catch (\Throwable $e) {
      $this->error($e->getMessage());
    }

    return $result;
  }

  /**
   * @return array
   */
  public function rules(): array
  {
    return [
      self::$columns::PRODUCT_ID->value               => 'required',
      self::$columns::PRODUCT_NAME->value             => 'required',
      self::$columns::BRAND->value                    => '',
      self::$columns::PRICE->value                    => '',
      self::$columns::DESCRIPTION->value              => '',
      self::$columns::PACKAGING->value                => '',
      self::$columns::EAN->value                      => 'numeric',
      self::$columns::PRODUCT_NUMBER->value           => '',
      self::$columns::ON_SALE->value                  => '',
      self::$columns::MAIN_CATEGORY->value            => 'required',
      self::$columns::COMMAND->value                  => '',
      self::$columns::SUB_CATEGORY->value             => '',
    ];
  }

  private function error(string $message, string $icon = 'exclamation-circle')
  {
    $this->imported_by->notify(
      NovaNotification::make()
        ->message($message)
        // ->action('Download', URL::remote('https://example.com/report.pdf'))
        ->icon($icon)
        ->type('error')
    );
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

    $product->name            = $row[self::$columns::PRODUCT_NAME->value];
    $product->slug            = Str::slug($row[self::$columns::PRODUCT_NAME->value], '-');
    if (array_key_exists(self::$columns::PACKAGING->value, $row)) {
      $product->packaging       = $row[self::$columns::PACKAGING->value];
    }
    if (array_key_exists(self::$columns::DESCRIPTION->value, $row)) {
      $product->description     = $row[self::$columns::DESCRIPTION->value];
    }
    if (array_key_exists(self::$columns::EAN->value, $row) && is_numeric($row[self::$columns::EAN->value])) {
      $product->ean             = $row[self::$columns::EAN->value];
    }
    if (array_key_exists(self::$columns::PRODUCT_NUMBER->value, $row)) {
      $product->product_number  = $row[self::$columns::PRODUCT_NUMBER->value];
    }
    $product->price           = $row[self::$columns::PRICE->value];
    $product->on_sale         = (array_key_exists(self::$columns::ON_SALE->value, $row) && strtolower($row[self::$columns::ON_SALE->value]) === 'y');
    $product->status          = ($is_active) ? BasicStatus::Active->value : BasicStatus::Inactive->value;

    /** 
     * @var Brand $brand The product's brand.
     */
    $brand = Brand::firstOrNew([
      'slug'        => Str::slug($row[self::$columns::BRAND->value]),
    ], [ //- Fill up data.
      'name'        => $row[self::$columns::BRAND->value],
      'is_featured' => false
    ]);
    if ($brand->exists) {
      $this->tracker->increaseBrandModified();
    } else {
      $this->tracker->increaseBrandInserted();
    }
    $brand->save();

    // Connect brand to product.
    $product->brand()->associate($brand);

    // dump($product);

    $this->save_images($product, $row);

    if ($product->exists) {
      $this->tracker->increaseProductModified();
      $is_new = false;
    } else {
      $is_new = true;
      $this->tracker->increaseProductInserted();
    }

    $product->save();

    /** Upload categories...
     */
    if (!$is_new) { //- If modifying product we detach from all categories.
      $product->categories()->detach();
    }

    /** Check is there category & adding to categories.
     * 
     * This method will also insert or modify categories.
     */
    $this->attach_categories($product, $row);

    return $product;
  }

  /**
   * @param $row
   * 
   * @return Product
   */
  private function delete_product($row)
  {
    return Product::where('product_id', '=', $row[self::$columns::PRODUCT_ID->value])->delete();
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

      foreach ($__images as $string)
      {
        if (!array_key_exists($index, $images))
        {
          $images[$index] = '';
        }
        $images[$index] .= $string;
        
        if (!str_contains($string, 'data:image/'))
        {
          $index++;
        }
      }
      // dump($images);
      foreach ($images as $string)
      {
        
        if (str_contains($string, 'data:image/')) { //- base64 image
          $product
            ->addMediaFromBase64($string, ["image/jpeg", "image/png"])
            ->toMediaCollection(Product::MEDIA_COLLECTION);
        }

        if (str_contains($string, 'http')) { //- http image
          $product
            ->addMediaFromUrl($string)
            ->toMediaCollection(Product::MEDIA_COLLECTION);
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
  private function attach_categories(Product $product, array $row): array
  {
    $columns  = array_keys($row);

    $result   = [];

    for ($categories_index = 1; $categories_index <= 3; $categories_index++) {
      $main_category_column = Arr::first(preg_grep(($categories_index > 1) ? "/" . self::$columns::MAIN_CATEGORY->value . "[^\d]*{$categories_index}[^\w]*/" : "/" . self::$columns::MAIN_CATEGORY->value . "/", $columns));

      if ($row[$main_category_column]) {
        $main_category = Category::firstOrCreate([
          'slug'        => Str::slug($row[$main_category_column]),
          'parent_id'   => null
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

          if (isset($row[$sub_category_column]) && !is_null($row[$sub_category_column])) {
            $sub_category = Category::firstOrNew([
              'slug'        => Str::slug($row[$sub_category_column]),
              'parent_id'   => $category->id
            ], [
              'name'        => $row[$sub_category_column]
            ]);

            if (!$sub_category->exists) {
              $this->tracker->increaseCategoryInserted();
              $sub_category->save();
              $sub_category->makeChildOf($category);
            } else {
              $this->tracker->increaseCategoryModified();
            }

            $category = $sub_category;
          }
        }
        $result[$categories_index] = $category;
      }
    }

    foreach ($result as $category_index => $category) {
      $counter = $category->products()->count();
      $product->categories()->attach($category, [
        'is_main' => ($category_index == 1),
        'order'   => $counter++,
      ]);
    }

    return $result;
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
