<?php

namespace App\Jobs;

ini_set('memory_limit', '4000M');

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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Neon\Models\Statuses\BasicStatus;

class ADOBAllProductImportJob implements ShouldQueue
{
  use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  const MAX_SUB_CATEGORY_COUNT    = 5;

  private $record;
  /**
   * Create a new job instance.
   */
  public function __construct(
    protected $records,
    protected $records_has_header = true,
    protected $columns,
    protected ProductImport $import
  ) {
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    // $this->save_brand();

    // $this->save_categories();

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
        $this->record = array_combine($header, $record_data);

        if ($this->to_save()) {
          $this->save_product();
        } elseif ($this->to_delete()) {
          $this->delete_product();
        }
      }
    }

    /** After all records are inserted, we save the numbers.
     */
    DB::beginTransaction();

    try
    {
      DB::table('brand_category_counts')->truncate();

      $data = DB::table('categories')
        ->join('category_product', 'category_product.category_id', '=', 'categories.id')
        ->join('products', 'products.id', '=', 'category_product.product_id')
        ->join('brands', 'brands.id', '=', 'products.brand_id')
        ->selectRaw('UUID() AS id, categories.id AS category_id, brands.id AS brand_id, COUNT(products.id) AS counts, NOW() AS created_at, NOW() AS updated_at')
        ->whereNull('categories.deleted_at', 'and')
        ->whereNull('products.deleted_at', 'and')
        ->whereNull('brands.deleted_at', 'and')
        ->where('products.status', '=', BasicStatus::Active->value)
        ->groupBy('categories.id')
        ->groupBy('brands.id')
        ->get()
        ->map(fn($value) => (array) $value) // Make all items to array.
        ->toArray();

      DB::table('brand_category_counts')->insertOrIgnore($data);

      DB::commit();

    //   $this->info('Counters updated successfully!');
    } catch (\Throwable $e) {
      DB::rollback();

    //   $this->error('Fuck.');
    }
  }

  public function record(): array
  {
    return (array) $this->record;
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

          Log::channel('import')->info('Brand imported: '.$record[$this->columns::BRAND->value]);
        }
      }
    }
  }

  private function save_categories()
  {
    $result = [];
    /** Get the header...
     */
    if ($this->records_has_header)
    {
      $header = $this->import->data['file'][0];
    }

    foreach ($this->records as $record_index => $record_data)
    {
      if ($record_data != $header)
      {
        /**
         * @var array $record Associative array of the given record.
         */
        $record = array_combine($header, $record_data);

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
            Log::channel('import')->info('Main category imported: '.$record[$main_category_column]);

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
                Log::channel('import')->info(' ⌞ Sub category imported: '.$record[$sub_category_column]);

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
        Log::channel('import')->info('Product ID connection imported: '.$record[$this->columns::PRODUCT_ID->value].': '.implode(', ', $result));
      }
    }
  }

  /**
   * @return Product
   */
  private function save_product(): void
  {
    $validator = Validator::make($this->record, $this->rules());

    if ($validator->fails()) {
      $error = ValidationException::withMessages($validator->messages()->toArray());

      throw $error;
    }

    $is_new = null;

    $product = Product::firstOrNew([
      'product_id' => $this->record[$this->columns::PRODUCT_ID->value]
    ]);

    $product->name            = $this->record[$this->columns::PRODUCT_NAME->value];
    $product->slug            = $this->record[$this->columns::PRODUCT_ID->value] . '-' . Str::slug($this->record[$this->columns::PRODUCT_NAME->value], '-');
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
    $brand = Brand::where('slug', '=', Str::slug($this->record[$this->columns::BRAND->value]))->first();

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
    Log::channel('import')->info('Product saved: '.$this->record[$this->columns::PRODUCT_ID->value]);

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
    $this->attach_categories($product, $this->import);

    // return $product;
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
   * @param ProductImport $import
   * @return void
   */
  private function attach_categories(Product $product, ProductImport $import): void
  {
    $categories = (array_key_exists($product->product_id, $import->getCategoryIds())) ? $import->getCategoryIds()[$product->product_id] : null;

    if (is_array($categories) && !empty($categories)) {
      foreach ($categories as $category_index => $category_id) {
        /** @var Category $category to attach to the product.
         */
        $category = Category::find($category_id);

        /** @var int Number of connected items.
         */
        $counter  = $category->products()->count();

        /** Attach the category and product to each other.
         */
        $product->categories()->attach($category, [
          'is_main' => ($category_index == 1),
          'order'   => $counter++,
        ]);
        Log::channel('import')->info('Product category attached: '.$this->record[$this->columns::PRODUCT_ID->value].' >> '.$category_id);
      }
    }

    // return $result;
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
          Log::channel('import')->info('Product image queried: '.$this->record[$this->columns::PRODUCT_ID->value].' >> '.$string);
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