<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Columns;
use App\Models\ProductExport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Neon\Admin\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use Maatwebsite\Excel\Concerns\WithEvents;
use Neon\Models\Statuses\BasicStatus;
use Maatwebsite\Excel\Events\ExportFailed;
use Maatwebsite\Excel\Events\AfterExport;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Throwable;

class ADOBProductsExport_new implements FromQuery, WithHeadings, WithEvents, ShouldQueue, WithChunkReading, WithMapping
{
  use Exportable;
    const UNCATEGORIZED_PRODUCT = '## KATEGORIZATLAN TERM. ##';

  const HEADING_ROW = 1;

  const MAX_SUB_CATEGORY_COUNT    = 5;

  static $columns = \App\Models\Columns\ADOBProductsExportColumns::class;

  public $exported_by;


  public $tracker;

    public $tries = 5;
    public $timeout = 120;


  public function __construct(ProductExport $tracker)
  {
    /** The exporter user. who need to set up for notifications... */
    $this->exported_by = $tracker->exported_by;
    /** The tracker for counts. */
    $this->tracker = $tracker;
    ini_set('memory_limit', '1024M');
  }

  /** Handle heading row.
   *
   * @see https://docs.laravel-excel.com/3.1/exports/heading-row.html
   *
   * @return int Row's index number.
   */
  public function headingRow(): int
  {
    return self::HEADING_ROW;
  }

  public function chunkSize(): int
  {
      return 500;
  }

  public function headings(): array
  {
    return array_column(self::$columns::cases(), 'value');
  }

  public function map($row): array
  {
//    $sizes    = []; //- Collecting sizes...
//      $size_sum = 0; //- Summarized size of items...
//      $urls     = []; //- Collecting URLs...
//      $paths    = []; //- Categories...
//    $media      = $row->getMedia(Product::MEDIA_COLLECTION);

//    foreach ($media as $img) {
//        $urls[]     = $img->getUrl();
//        $sizes[]    = $img->file_name . ' (' . size_format($img->size) . ')';
//        $size_sum   += $img->size;
//      }


      return [
        $row->product_id, // PRODUCT_ID
        $row->name, // PRODUCT_NAME
        $row->brand?->name, // BRAND_NAME
        $row->ean, // PRODUCT_EAN
        $row->price, // PRODUCT_PRICE
        $this->generateCategories($row, true), // PRODUCT_MAIN_CATEGORY
        $this->generateCategories($row, false), // PRODUCT_CATEGORIES
//        route('product.show', ['slug' => $row->slug]), // PRODUCT_URL
//        $media->count(), // IMAGE_COUNT
//        implode(';', $sizes), // IMAGE_SIZES
//        ($size_sum > 0) ? size_format($size_sum) : '', // IMAGE_SIZE_SUM
//        ($row->status == BasicStatus::Active) ? '1' : '0', // PRODUCT_STATUS
//        implode(';', $urls), // IMAGE_LINKS
//        strip_tags($row->description), // PRODUCT_DESCRIPTION
      ];
  }

  /**
   * @return \Illuminate\Support\Collection
   */
  public function query()
  {
    $x = Product::query()
      ->withoutGlobalScopes()
      ->with('brand');

    return $x;


    // $products = Product::withoutGlobalScopes([
    //     \Neon\Models\Scopes\ActiveScope::class
    //   ])
    //   ->orderBy('product_id')
    //   ->get();

    // foreach ($products as $product) {
    //   /** @var Collection Getting media collection.
    //    */
    //   $media      = $product->getMedia(Product::MEDIA_COLLECTION);
    //   $categories = $product->categories()->get();

    //   $sizes    = []; //- Collecting sizes...
    //   $size_sum = 0; //- Summarized size of items...
    //   $urls     = []; //- Collecting URLs...
    //   $paths    = []; //- Categories...

    //   // foreach ($categories as $category) {
    //   //   $path = [];
    //   //   foreach ($category->getAncestorsAndSelf() as $path_item) {
    //   //     array_unshift($path, $path_item->name);
    //   //   }
    //   //   $paths[] = implode('\\', $path);
    //   // }

    //   // foreach ($media as $img) {
    //   //   $urls[]     = $img->getUrl();
    //   //   $sizes[]    = $img->file_name . ' (' . size_format($img->size) . ')';
    //   //   $size_sum   += $img->size;
    //   // }

    //   $result[] = [
    //     $product->product_id, // PRODUCT_ID
    //     $product->name, // PRODUCT_NAME
    //     $product->brand()->first()?->name, // BRAND_NAME
    //     $product->ean, // PRODUCT_EAN
    //     $product->price, // PRODUCT_PRICE
    //     (count($paths) > 0) ? $paths[0] : '', // PRODUCT_MAIN_CATEGORY
    //     implode(', ', array_slice($paths, 1)), // PRODUCT_CATEGORIES
    //     route('product.show', ['slug' => $product->slug]), // PRODUCT_URL
    //     $media->count(), // IMAGE_COUNT
    //     implode(';', $sizes), // IMAGE_SIZES
    //     ($size_sum > 0) ? size_format($size_sum) : '', // IMAGE_SIZE_SUM
    //     ($product->status == BasicStatus::Active) ? '1' : '0', // PRODUCT_STATUS
    //     implode(';', $urls), // IMAGE_LINKS
    //     strip_tags($product->description), // PRODUCT_DESCRIPTION
    //   ];
    // }

    // return collect($result);
  }

  public function failed(Throwable $exception): void
  {
    $this->error($exception->getMessage());
  }

    /**
     * Generates a string representation of the categories associated with a given product.
     *
     * @param \App\Models\Product $product The product object for which the categories need to be generated.
     * @param bool $isMain Indicates whether to fetch main categories or not.
     * @return string A semicolon-separated string of category paths. Each path is a hierarchy of category names separated by slashes (/).
     *                If the product has no categories, it returns the constant UNCATEGORIZED_PRODUCT.
     */
    private function generateCategories($product, bool $isMain = false)
    {
        $categoriesCell = [];
        $categories = $product->categories()->where('is_main', $isMain)->get();

        if (count($categories) > 0) {

            foreach ($categories as $category) {
                $cat = $category->ancestorsAndSelf()->get()->toHierarchy();
                array_push($categoriesCell, rtrim(self::printTree($cat), '/'));
            }

            return implode("; ", $categoriesCell);

        } else {
            return self::UNCATEGORIZED_PRODUCT;
        }

    }

    static public function printTree($root)
    {
        $str = '';

        foreach ($root as $r) {
            $str .= $r->name . '/';

            if (count($r->children) > 0) {
                $str .= self::printTree($r->children);
            }
        }

        return $str;
    }



    private function error(string $message, string $icon = 'exclamation-circle')
  {
    $this->tracker->addFail($message);

    Notification::make()
      ->title('Exportálás folyamata...')
      ->body($message)
      ->danger()
      ->sendToDatabase($this->exported_by);
  }


  public function registerEvents(): array
  {
    return [
      BeforeExport::class => function (BeforeExport $event)
      {
        $this->tracker->status = 'running';
        $this->tracker->save();

        Notification::make()
          ->title('Exportálás folyamata...')
          ->body(' Termékek exportálása...')
          ->info()
          ->sendToDatabase($this->tracker->exported_by);
      },

      // ExportFaile::class => function (ExportFailed $event)
      // {
      //   $this->tracker->addFail($event->getException()->getMessage());
      //   $this->tracker->status = 'failed';
      //   $this->tracker->save();
      //   $this->error($event->getException()->getMessage());
      // },

      AfterSheet::class => function(AfterSheet $event)
      {
        $this->tracker->status = 'finished';
        $this->tracker->finished_at = now();
        $this->tracker->save();

        Notification::make()
          ->title('Exportálás folyamata...')
          ->body((($this->tracker->fails_counter > 0) ? 'Végeztünk.' : 'Sikeresen végeztünk!').' A keresett állomány itt tölthető le: <a href="'.Storage::url($this->tracker->file).'">'.$this->tracker->file.'</a>')
          ->success()
          ->sendToDatabase($this->tracker->exported_by);
      }
    ];
  }
}
