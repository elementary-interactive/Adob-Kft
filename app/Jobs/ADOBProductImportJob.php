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
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Neon\Models\Statuses\BasicStatus;

class ADOBProductImportJob implements ShouldQueue
{
  use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  const MAX_SUB_CATEGORY_COUNT    = 5;

  /**
   * Create a new job instance.
   */
  public function __construct(
    protected $record,
    protected $columns,
    protected ProductImport $import
  ) {
  }


  /**
   * Execute the job.
   */
  public function handle(): void
  {
    if ($this->to_save()) {
      $this->save_product();
    } elseif ($this->to_delete()) {
      $this->delete_product();
    }
  }

  /**
   * @return array
   */
  public function rules(): array
  {
    return [
      $this->columns::PRODUCT_ID->value               => 'required',
      $this->columns::PRODUCT_NAME->value             => 'required',
      // $this->columns::BRAND->value                    => '',
      // $this->columns::PRICE->value                    => '',
      // $this->columns::DESCRIPTION->value              => '',
      // $this->columns::PACKAGING->value                => '',
      $this->columns::EAN->value                      => 'numeric',
      // $this->columns::PRODUCT_NUMBER->value           => '',
      // $this->columns::ON_SALE->value                  => '',
      $this->columns::MAIN_CATEGORY->value            => 'required',
      // $this->columns::COMMAND->value                  => '',
      // $this->columns::SUB_CATEGORY->value             => '',
    ];
  }

  /**
   * @return Product
   */
  private function save_product(): Product|null
  {
    $validator = Validator::make($this->record, $this->rules());

    if ($validator->fails()) {
      dump($validator->messages());

      $error = ValidationException::withMessages((array) $validator->messages());

      throw $error;
    }

    $is_new = null;

    $product = Product::firstOrNew([
      'product_id' => $this->record[$this->columns::PRODUCT_ID->value]
    ]);

    $product->name            = $this->record[$this->columns::PRODUCT_NAME->value];
    $product->slug            = Str::slug($this->record[$this->columns::PRODUCT_NAME->value], '-');
    if (array_key_exists($this->columns::PACKAGING->value, $this->record)) {
      $product->packaging       = $this->record[$this->columns::PACKAGING->value];
    }
    if (array_key_exists($this->columns::DESCRIPTION->value, $this->record)) {
      $product->description     = $this->record[$this->columns::DESCRIPTION->value];
    }
    if (array_key_exists($this->columns::EAN->value, $this->record) && is_numeric($this->record[$this->columns::EAN->value])) {
      $product->ean             = $this->record[$this->columns::EAN->value];
    }
    if (array_key_exists($this->columns::PRODUCT_NUMBER->value, $this->record)) {
      $product->product_number  = $this->record[$this->columns::PRODUCT_NUMBER->value];
    }
    $product->price           = $this->record[$this->columns::PRICE->value];
    $product->on_sale         = (array_key_exists($this->columns::ON_SALE->value, $this->record) && strtolower($this->record[$this->columns::ON_SALE->value]) === 'y');
    $product->status          = ($this->is_active()) ? BasicStatus::Active->value : BasicStatus::Inactive->value;

    /** 
     * @var Brand $brand The product's brand.
     */
    $brand = Brand::firstOrNew([
      'slug'        => Str::slug($this->record[$this->columns::BRAND->value]),
    ], [ //- Fill up data.
      'name'        => $this->record[$this->columns::BRAND->value],
      'is_featured' => false
    ]);
    dump(Str::slug($this->record[$this->columns::BRAND->value]), $brand);
    if ($brand->exists) {
      $this->import->increaseBrandModified();
    } else {
      $this->import->increaseBrandInserted();
    }
    // DB::transaction(function () use ($brand) {
      $brand->save();
    // }, 5);
        
    // Connect brand to product.
    $product->brand()->associate($brand);
    // Associating is not saving, so we handle brands here, and then, when prodcut's other parts are also done, save to database.

    // dump($product);
    if ($product->exists) {
      $this->import->increaseProductModified();
      $is_new = false;
    } else {
      $is_new = true;
      $this->import->increaseProductInserted();
    }
    // DB::transaction(function () use ($product) {
      $product->save();
    // }, 5);

    /** Upload images...
     */
    $this->handle_images($product);

    /** Upload categories...
     */
    if (!$is_new) { //- If modifying product we detach from all categories.
      $product->categories()->detach();
    }
    /** Check is there category & adding to categories.
     * 
     * This method will also insert or modify categories.
     */
    $this->attach_categories($product);

    return $product;
  }

  /**
   * @return Product
   */
  private function delete_product()
  {
    $product = Product::where('product_id', '=', $this->record[$this->columns::PRODUCT_ID->value])->first();
    $product->delete();

    return $product;
  }

  /** Parse and save categories. Returns with nodes where to product should be
   * attached.
   * 
   * @param Product $product
   * 
   * @return array $categories
   */
  private function attach_categories(Product $product): array
  {
    $columns  = array_keys($this->record);

    $result   = [];

    for ($categories_index = 1; $categories_index <= 3; $categories_index++) {
      $main_category_column = Arr::first(preg_grep(($categories_index > 1) ? "/" . $this->columns::MAIN_CATEGORY->value . "[^\d]*{$categories_index}[^\w]*/" : "/" . $this->columns::MAIN_CATEGORY->value . "/", $columns));

      if ($this->record[$main_category_column]) {
        $main_category = Category::firstOrCreate([
          'slug'        => Str::slug($this->record[$main_category_column]),
          'parent_id'   => null
        ], [
          'name'        => $this->record[$main_category_column],
          'description' => $this->record[$main_category_column]
        ]);

        $category = null;

        for ($sub_category_count = 1; $sub_category_count <= self::MAX_SUB_CATEGORY_COUNT; $sub_category_count++) {
          if (is_null($category)) {
            $category = $main_category;
          }
          $sub_category_column = Arr::first(preg_grep(($categories_index > 1) ? "/" . $this->columns::SUB_CATEGORY->value . "{$sub_category_count}[^\d]*{$categories_index}[^\w]*/" : "/" . $this->columns::SUB_CATEGORY->value . "{$sub_category_count}/", $columns));

          if (isset($this->record[$sub_category_column]) && !is_null($this->record[$sub_category_column])) {
            $sub_category = Category::firstOrNew([
              'slug'        => Str::slug($this->record[$sub_category_column]),
              'parent_id'   => $category->id
            ], [
              'name'        => $this->record[$sub_category_column]
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

      foreach ($images as $string) {
        if (Str::startsWith($string, 'data:image/')) { //- base64 image
          $product
            ->addMediaFromBase64($string, ["image/jpeg", "image/png"])
            ->toMediaCollection(Product::MEDIA_COLLECTION);
        }

        if (Str::startsWith($string, 'http')) { //- http image
          $media = $product
            ->addMediaFromUrl($string)
            ->preservingOriginal()
            ->toMediaCollection(Product::MEDIA_COLLECTION);
          $media->save();
        }
      }
    }
  }

  private function to_save(): bool
  {
    return (strtolower($this->record[$this->columns::COMMAND->value]) === 'y' || strtolower($this->record[$this->columns::COMMAND->value]) === 'i');
  }

  private function is_active(): bool
  {
    return (strtolower($this->record[$this->columns::COMMAND->value]) === 'y');
  }

  private function to_delete(): bool
  {
    return (strtolower($this->record[$this->columns::COMMAND->value]) === 'd');
  }
}
