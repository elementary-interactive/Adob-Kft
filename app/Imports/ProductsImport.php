<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Neon\Admin\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Str;

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

  const COLUMN_WEB                = 'web';
  const COLUMN_PRODUCT_ID         = 'cikkszam';
  const COLUMN_PRODUCT_NAME       = 'megnevezes';
  const COLUMN_BRAND              = 'marka';
  const COLUMN_PRICE              = 'webar';
  const COLUMN_DESCRIPTION        = 'leiras';
  const COLUMN_DESCRIPTION_UPDATE = 'webleir';
  const COLUMN_PACKAGING          = 'csomagolas';
  const COLUMN_EAN                = 'ean';
  const COLUMN_PRODUCT_NUMBER     = 'termekszam';
  const COLUMN_ON_SALE            = 'akcios';
  const COLUMN_MAIN_CATEGORY      = 'main_kat';
  const COLUMN_COMMAND            = 'web';
  const COLUMN_SUB_CATEGORY       = 'alkat';
  const COLUMN_DESC_TO_CATEGORY   = 'webkatleir';

  public $importedBy;

  private $headerRow;
  private $headerErrors = [];

  private $aggregateProduct = "webagrp";
  private $agrIdPostfix = "-AGR";
  private $track;
  private $category_group_count = 0;

  public function __construct(Admin $importedBy)
  {
    $this->importedBy = $importedBy;
  }

  public function model(array $row)
  {

    /**
     * @todo: Header based model validation.
     */

    // if (!array_key_exists('web', $row)) {
    //   $this->error('Nem található \'web\' oszlop!');
    // }
    // dd($row);
    // dd($this->importedBy);

    // return new Product([
    //   'name'  => $row['name'],
    //   'email' => $row['email'],
    // ]);

    $isAggregateProduct = false;

    if ($row[self::COLUMN_WEB] == 'y' || $row[self::COLUMN_WEB] == 'Y') {
      $aggregateId = $this->saveProduct($row, $isAggregateProduct, true);
    }

    if ($row[self::COLUMN_WEB] == 'i' || $row[self::COLUMN_WEB] == 'I') {
      $aggregateId = $this->saveProduct($row, $isAggregateProduct, false);
    }

    if ($row[self::COLUMN_WEB] == 'd' || $row[self::COLUMN_WEB] == 'D') {
      // $this->deleteProduct($row, $isAggregateProduct);
    }
  }

  /**
   * @return array
   */
  public function rules(): array
  {
    return [
      self::COLUMN_WEB => 'required',
    ];
  }

  private function error(string $message, string $icon = 'exclamation')
  {
    $this->importedBy->notify(
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
    $isNew = null;

    $pid = $isAggregate ? $this->generateAgrProductID($row[self::COLUMN_PRODUCT_ID], $this->agrIdPostfix) : $row[self::COLUMN_PRODUCT_ID];

    $product = Product::firstOrNew(['product_id' => $pid]);
    $product->name            = $row[self::COLUMN_PRODUCT_NAME];
    $product->slug            = Str::slug($row[self::COLUMN_PRODUCT_NAME], '-');
    $product->packaging       = array_key_exists(self::COLUMN_PACKAGING, $row) ? $row[self::COLUMN_PACKAGING] : null;
    $product->description     = (
      ($row[self::COLUMN_DESCRIPTION_UPDATE] == "y" || $row[self::COLUMN_DESCRIPTION_UPDATE] == "Y") &&
      array_key_exists(self::COLUMN_DESCRIPTION, $row)
    ) ? $row[self::COLUMN_DESCRIPTION] : null;
    $product->ean             = array_key_exists(self::COLUMN_EAN, $row) ? (is_numeric($row[self::COLUMN_EAN]) ? $row[self::COLUMN_EAN] : null) : null;
    $product->product_number  = array_key_exists(self::COLUMN_PRODUCT_NUMBER, $row) ? $row[self::COLUMN_PRODUCT_NUMBER] : null;
    $product->price           = $row[self::COLUMN_PRICE];
    $product->on_sale         = (array_key_exists(self::COLUMN_ON_SALE, $row) && ($row[self::COLUMN_ON_SALE] == "y" || $row[self::COLUMN_ON_SALE] == "Y"));
    $product->status          = ($is_active) ? BasicStatus::Active->value : BasicStatus::Inactive->value;

    /** 
     * @var Brand $brand The product's brand.
     */
    $brand = Brand::firstOrCreate([
      'name'  => $row[self::COLUMN_BRAND]
    ], [ //- Fill up data.
      'slug'        => Str::slug($row[self::COLUMN_BRAND]),
      'is_featured' => false
    ]);
    $product->brand()->associate($brand);

    if ($product->exists) {
      // $this->track->increaseModified();
      $isNew = false;
    } else {
      $isNew = true;
      // $this->track->increaseInserted();
    }

    $product->save();

    /** Check is there category & adding to categories
     */
    $categories = $this->extract_categories($row);

    // leírások feltöltése a kategóriákhoz
    $saveDescToCategory = null;
    $category_description = null;

    if (array_key_exists(self::COLUMN_DESC_TO_CATEGORY, $row)) {

      $saveDescToCategory = $this->calculate_target_category_index($row[self::COLUMN_DESC_TO_CATEGORY]);
      if (array_key_exists(self::COLUMN_DESCRIPTION, $row)) {
        $category_description = $row[self::COLUMN_DESCRIPTION];
      }
    }

    /** Upload categories...
     */
    if ($isNew) {
      $this->attach_product_to_categories(
        $product,
        $this->save_categories($categories, $saveDescToCategory, $category_description)
      );
    } else {
      $product->categories()->detach();

      $this->attach_product_to_categories(
        $product,
        $this->save_categories($categories, $saveDescToCategory, $category_description)
      );
    }

    return $product;
  }

  private function generateAgrProductID($col, $postfix)
  {
    if (is_null($col)) {
      $col = Carbon::now()->getTimestamp() . $postfix;
    }

    return $col;
  }

  /**
   * @param $row
   * @return array
   */
  private function extract_categories($row)
  {
    $categories = [];

    if (array_key_exists(self::COLUMN_MAIN_CATEGORY, $row)) {
      $base_category_group[] = $row[self::COLUMN_MAIN_CATEGORY];

      for ($i = 1; $i <= self::MAX_SUB_CATEGORY_COUNT; $i++) {
        if (array_key_exists(self::COLUMN_SUB_CATEGORY . $i, $row)) {
          $base_category_group[] = $row[self::COLUMN_SUB_CATEGORY . $i];
        } else {
          break;
        }
      }
      $categories[] = $base_category_group;
    }

    //Additional category Groups
    for ($i = 2; $i <= $this->category_group_count; $i++) {
      $categoryGroup = [];

      //pushing main_kat
      if (array_key_exists(self::COLUMN_MAIN_CATEGORY . "_" . $i, $row)) {
        $categoryGroup[] = $row[self::COLUMN_MAIN_CATEGORY . "_" . $i];

        for ($j = 1; $j <= self::MAX_SUB_CATEGORY_COUNT; $j++) {
          if (array_key_exists(self::COLUMN_SUB_CATEGORY . $j . "_" . $i, $row)) {
            $categoryGroup[] = $row[self::COLUMN_SUB_CATEGORY . $j . "_" . $i];
          } else {
            break;
          }
        }
        $categories[] = $categoryGroup;
      }
    }

    return $categories;
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

  /**
   * $targetCategoryString: pl "3,2" -> 3 kat csop 2. alkategoriajaba kell menteni
   *
   * @param $categories
   * @param null $targetCategoryIndexes
   * @param null $catDescription
   * @return array
   */
  private function save_categories($categories, $targetCategoryIndexes = null, $catDescription = null)
  {

    $lastCategoryNodes = []; // levelek
    $j = 0;

    foreach ($categories as $group) {

      $parent = null;
      $node = null;

      for ($i = 0; $i < count($group); $i++) {
        $currentCategoryName = $group[$i];
        $catDesc = null;

        if ($targetCategoryIndexes !== null && $j == $targetCategoryIndexes[0] && $i == $targetCategoryIndexes[1]) {
          $catDesc = $catDescription;
        }

        if ($i === 0 && $currentCategoryName === null) {
          break;
        } else {

          if ($currentCategoryName !== null) {
            $parentId = ($parent === null ? null : isset($parent->id)) ? $parent->id : null;

            $node = Category::where('name', '=', $currentCategoryName)
              ->where('parent_id', '=', $parentId)
              ->first();

            if ($node !== null) {

              if ($catDesc !== null) {
                $node->description = $catDesc;
                $node->save();
              }

              $parent = $node;
              continue;
            } else {
              $node = Category::create([
                'name' => $currentCategoryName,
                'description' => $catDesc
              ]);

              if ($parent !== null) {
                $node->makeChildOf($parent);
              }
              $parent = $node;
            }
          } else {
            break;
          }
        }
      }

      array_push($lastCategoryNodes, $node);
      $j += 1;
    }


    return $lastCategoryNodes;
  }

  private function attach_product_to_categories($product, $categories)
  {
    if ($product->exists && count($categories) > 0) {
      foreach ($categories as $cat) {
        $product->categories()->attach($cat);
      }
      return true;
    }

    return false;
  }
}
