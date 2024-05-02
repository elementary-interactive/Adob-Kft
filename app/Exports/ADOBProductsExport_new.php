<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Columns;
use App\Models\ProductExport;
use App\Models\ProductImport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Neon\Admin\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Laravel\Nova\Notifications\NovaNotification;
use Laravel\Nova\Notifications\NovaChannel;
use Laravel\Nova\URL;
use Maatwebsite\Excel\Concerns\WithEvents;
use Neon\Models\Statuses\BasicStatus;
use Maatwebsite\Excel\Events\ImportFailed;
use Maatwebsite\Excel\Events\AfterImport;


class ADOBProductsExport_new implements FromCollection, WithHeadingRow, WithEvents //, ShouldQueue
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

  static $columns = \App\Models\Columns\ADOBProductsExportColumns::class;

  public $imported_by;

  public $headerRow;

  public $tracker;

  public function __construct(ProductExport $tracker)
  {
    /** The importer user. who need to set up for notifications... */
    $this->imported_by  = $tracker->imported_by;
    /** The tracker for counts. */
    $this->tracker      = $tracker;
  }

  // public function __destruct()
  // {
  //   $this->tracker->save();
  // }

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
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    $result = [
      'header' => array_column(self::$columns::cases(), 'value'),
    ];

    $products = Product::withoutGlobalScopes([
        \Neon\Models\Scopes\ActiveScope::class
      ])
      ->orderBy('product_id')
      ->get();

    foreach ($products as $product) {
      /** @var Collection Getting media collection.
       */
      $media      = $product->getMedia(Product::MEDIA_COLLECTION);
      $categories = $product->categories()->get();

      $sizes    = []; //- Collecting sizes...
      $size_sum = 0; //- Summarized size of items...
      $urls     = []; //- Collecting URLs...
      $paths    = []; //- Categories...

      foreach ($categories as $category) {
        $path = [];
        foreach ($category->getAncestorsAndSelf() as $path_item) {
          array_unshift($path, $path_item->name);
        }
        $paths[] = implode('\\', $path);
      }

      foreach ($media as $img) {
        $urls[]     = $img->getUrl();
        $sizes[]    = $img->file_name . ' (' . size_format($img->size) . ')';
        $size_sum   += $img->size;
      }

      $result[] = [
        $product->product_id, // PRODUCT_ID
        $product->name, // PRODUCT_NAME
        $product->brand()->first()?->name, // BRAND_NAME
        $product->ean, // PRODUCT_EAN
        $product->price, // PRODUCT_PRICE
        $paths[0], // PRODUCT_MAIN_CATEGORY
        implode(', ', array_slice($paths, 1), // PRODUCT_CATEGORIES
        route('product.show', ['slug' => $product->slug]), // PRODUCT_URL
        $media->count(), // IMAGE_COUNT
        implode(';', $sizes), // IMAGE_SIZES
        $size_sum, // IMAGE_SIZE_SUM
        ($product->status == BasicStatus::Active) ? '1' : '0', // PRODUCT_STATUS
        implode('; ', $urls), // IMAGE_LINKS
        strip_tags($product->description), // PRODUCT_DESCRIPTION
      ];
    }

    return collect($result);
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
  private function saveProduct($row, $is_active = null): Product
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
      AfterImport::class => function (AfterImport $event) use ($me) {
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
}
