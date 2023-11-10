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
    dump($import->getCategoryIds());
    
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
