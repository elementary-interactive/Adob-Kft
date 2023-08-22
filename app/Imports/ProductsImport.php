<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
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
use Neon\Models\Statuses\BasicStatus;


class ProductsImport implements ToModel, WithValidation, WithHeadingRow, WithChunkReading, ShouldQueue
{
  use Importable;

  const HEADING_ROW = 1;

  const MAX_SUB_CATEGORY_COUNT    = 5;

  // const COLUMN_COMMAND->value                = 'COMMAND->value';
  // const COLUMN_PRODUCT_ID->value         = 'cikkszam';
  // const COLUMN_PRODUCT_NAME->value       = 'megnevezes';
  // const COLUMN_BRAND->value              = 'marka';
  // const COLUMN_PRICE              = 'COMMAND->valuear';
  // const COLUMN_DESCRIPTION->value        = 'leiras';
  // const COLUMN_DESCRIPTION_UPDATE->value = 'COMMAND->valueleir';
  // const COLUMN_PACKAGING->value          = 'csomagolas';
  // const COLUMN_EAN                = 'ean';
  // const COLUMN_PRODUCT_NUMBER->value     = 'termekszam';
  // const COLUMN_ON_SALE->value            = 'akcios';
  // const COLUMN_MAIN_CATEGORY->value      = 'main_kat';
  // const COLUMN_COMMAND            = 'COMMAND->value';
  // const COLUMN_SUB_CATEGORY->value       = 'alkat';
  // const COLUMN_DESCRIPTION_TO_CATEGORY->value   = 'COMMAND->valuekatleir';

  static $columns = \App\Models\Columns::class;

  public $imported_by;

  private $headerRow;
  private $headerErrors = [];

  private $aggregateProduct = "COMMAND->valueagrp";
  private $agrIdPostfix = "-AGR";

  private $category_group_count = 0;

  private $tracker;

  public function __construct(Admin $imported_by)
  {
    $this->imported_by  = $imported_by;
    $this->tracker      = new ProductImport([
      'imported_by' => $imported_by
    ]);
  }

  public function model(array $row)
  {

    /**
     * @todo: Header based model validation.
     */

    // if (!array_key_exists('COMMAND->value', $row)) {
    //   $this->error('Nem található \'COMMAND->value\' oszlop!');
    // }
    // dd($row);
    // dd($this->imported_by);

    // return new Product([
    //   'name'  => $row['name'],
    //   'email' => $row['email'],
    // ]);

    $isAggregateProduct = false;

    if (self::toSave($row)) {
      $aggregateId = $this->saveProduct($row, $isAggregateProduct, self::isActive($row));
    }

    if (self::toDelete($row)) {
      $this->deleteProduct($row, $isAggregateProduct);
    }
  }

  /**
   * @return array
   */
  public function rules(): array
  {
    return [
      self::$columns::PRODUCT_ID->value         => 'required',
      self::$columns::PRODUCT_NAME->value       => 'required',
      self::$columns::BRAND->value              => '',
      self::$columns::PRICE->value              => '',
      self::$columns::DESCRIPTION->value        => '',
      self::$columns::DESCRIPTION_UPDATE->value => '',
      self::$columns::PACKAGING->value          => '',
      self::$columns::EAN->value                => 'numeric',
      self::$columns::PRODUCT_NUMBER->value     => '',
      self::$columns::ON_SALE->value            => '',
      self::$columns::MAIN_CATEGORY->value      => 'required',
      self::$columns::COMMAND->value            => '',
      self::$columns::SUB_CATEGORY->value       => '',
      self::$columns::DESCRIPTION_TO_CATEGORY->value   => '',
    ];
  }

  private function error(string $message, string $icon = 'exclamation')
  {
    $this->imported_by->notify(
      NovaNotification::make()
        ->message($message)
        // ->action('Download', URL::remote('https://example.com/report.pdf'))
        ->icon($icon)
        ->type('error')
    );
  }

  public function headingRow(): int
  {
    return self::HEADING_ROW;
  }

  public function chunkSize(): int
  {
    return 1000;
  }

  /**
   * @param $row
   * @param bool $is_aggregate
   * @param null $is_active
   * @return string
   */
  private function saveProduct($row, $isAggregate = false, $is_active = null)
  {
    $is_new = null;

    $pid = $isAggregate ? $this->generateAgrProductID($row[self::$columns::PRODUCT_ID->value], $this->agrIdPostfix) : $row[self::$columns::PRODUCT_ID->value];

    $product = Product::firstOrNew(['product_id' => $pid]);
    $product->name            = $row[self::$columns::PRODUCT_NAME->value];
    $product->slug            = Str::slug($row[self::$columns::PRODUCT_NAME->value], '-');
    $product->packaging       = array_key_exists(self::$columns::PACKAGING->value, $row) ? $row[self::$columns::PACKAGING->value] : null;
    $product->description     = (
      (strtolower($row[self::$columns::DESCRIPTION_UPDATE->value]) === 'y') &&
      array_key_exists(self::$columns::DESCRIPTION->value, $row)
    ) ? $row[self::$columns::DESCRIPTION->value] : null;
    $product->ean             = array_key_exists(self::$columns::EAN->value, $row) ? (is_numeric($row[self::$columns::EAN->value]) ? $row[self::$columns::EAN->value] : null) : null;
    $product->product_number  = array_key_exists(self::$columns::PRODUCT_NUMBER->value, $row) ? $row[self::$columns::PRODUCT_NUMBER->value] : null;
    $product->price           = $row[self::$columns::PRICE->value];
    $product->on_sale         = (array_key_exists(self::$columns::ON_SALE->value, $row) && ($row[self::$columns::ON_SALE->value] == "y" || $row[self::$columns::ON_SALE->value] == "Y"));
    $product->status          = ($is_active) ? BasicStatus::Active->value : BasicStatus::Inactive->value;

    /** 
     * @var Brand $brand The product's brand.
     */
    $brand = Brand::firstOrCreate([
      'name'  => $row[self::$columns::BRAND->value]
    ], [ //- Fill up data.
      'slug'        => Str::slug($row[self::$columns::BRAND->value]),
      'is_featured' => false
    ]);
    // Connect brand to product.
    $product->brand()->associate($brand);

    if ($product->exists) {
      $this->tracker->increaseProductModified();
      $is_new = false;
    } else {
      $is_new = true;
      $this->tracker->increaseProductInserted();
    }

    $product->save();

    // leírások feltöltése a kategóriákhoz
    $saveDescToCategory = null;
    $category_description = null;

    if (array_key_exists(self::$columns::DESCRIPTION_TO_CATEGORY->value, $row)) {

      $saveDescToCategory = $this->calculate_target_category_index($row[self::$columns::DESCRIPTION_TO_CATEGORY->value]);
      if (array_key_exists(self::$columns::DESCRIPTION->value, $row)) {
        $category_description = $row[self::$columns::DESCRIPTION->value];
      }
    }

    /** Upload categories...
     */
    if (!$is_new)
    { //- If modifying product we detach from all categories.
      $product->categories()->detach();
    }

    /** Check is there category & adding to categories.
     * 
     * This method will also insert or modify categories.
     */
    $this->attach_categories($product, $row);

    return $product;
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

    for ($categories_index = 1; $categories_index <= 3; $categories_index++)
    {
      $main_category_column = Arr::first(preg_grep(($categories_index > 1) ? "/".self::$columns::MAIN_CATEGORY->value."[^\d]*{$categories_index}[^\w]*/" : "/".self::$columns::MAIN_CATEGORY->value."/", $columns));

      if ($row[$main_category_column])
      {
        $main_category = Category::firstOrCreate([
          'slug'        => Str::slug($row[$main_category_column]),
          'parent_id'   => null
        ], [
          'name'        => $row[$main_category_column],
          'description' => $row[$main_category_column]
        ]);

        $category = null;
        
        for ($sub_category_count = 1; $sub_category_count <= self::MAX_SUB_CATEGORY_COUNT; $sub_category_count++)
        {
          if (is_null($category))
          {
            $category = $main_category;
          }
          $sub_category_column = Arr::first(preg_grep(($categories_index > 1) ? "/".self::$columns::SUB_CATEGORY->value."{$sub_category_count}[^\d]*{$categories_index}[^\w]*/" : "/".self::$columns::SUB_CATEGORY->value."{$sub_category_count}/", $columns));
          
          if (isset($row[$sub_category_column]) && !is_null($row[$sub_category_column]))
          {
            $sub_category = Category::firstOrNew([
              'slug'        => Str::slug($row[$sub_category_column]),
              'parent_id'   => $category->id
            ], [
              'name'        => $row[$sub_category_column]
            ]);

            if (!$sub_category->exists)
            {
              /**
               * @todo increase category insert
               */
              $sub_category->save();
              $sub_category->makeChildOf($category);
            } else {

              /**
               * @todo increase category modify
               */
            }
            
            $category = $sub_category;
          }
        }
        $result[$categories_index] = $category;
      }
    }
    
    foreach ($result as $category_index => $category)
    {
      /** 
       * @todo Sorrendezés!!!
       */
      $product->categories()->attach($category, [
        'is_main' => ($category_index == 1)
      ]);
    }

    return $result;
  }

  private function generateAgrProductID($col, $postfix)
  {
    if (is_null($col)) {
      $col = Carbon::now()->getTimestamp() . $postfix;
    }

    return $col;
  }

  private function calculate_target_category_index($target_category)
  {
    $target_category_indexes = null;

    if ($target_category !== null) {
      $target_category_indexes = is_string($target_category) ?
        array_map('intval', explode(',', $target_category)) :
        array_map('intval', explode('.', $target_category));

      $target_category_indexes[0] = $target_category_indexes[0] - 1;
    }

    return $target_category_indexes;
  }

  public static function toSave(array $row): bool
  {
    return (strtolower($row[self::$columns::COMMAND->value]) === 'y' || strtolower($row[self::$columns::COMMAND->value]) === 'i');
  }

  public static function isActive(array $row): bool
  {
    return (strtolower($row[self::$columns::COMMAND->value]) === 'y');
  }

  public static function toDelete(array $row): bool
  {
    return (strtolower($row[self::$columns::COMMAND->value]) === 'd');
  }
}
