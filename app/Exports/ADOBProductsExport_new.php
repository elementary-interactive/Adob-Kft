<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\ProductExport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\ExportFailed;
use Maatwebsite\Excel\Events\AfterExport;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Neon\Models\Statuses\BasicStatus;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class ADOBProductsExport_new implements FromQuery, WithHeadings, WithEvents, ShouldQueue, WithChunkReading, WithMapping
{
    use Exportable;

    const UNCATEGORIZED_PRODUCT = '## KATEGORIZATLAN TERM. ##';
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
        $media = $row->getMedia(Product::MEDIA_COLLECTION);
        $mediaInfos = $this->getMediaInfos($media);

        return [
            $row->product_id, // PRODUCT_ID
            $row->name, // PRODUCT_NAME
            $row->brand?->name, // BRAND_NAME
            $row->ean, // PRODUCT_EAN
            $row->price, // PRODUCT_PRICE
            $this->generateCategories($row, true), // PRODUCT_MAIN_CATEGORY
            $this->generateCategories($row, false), // PRODUCT_CATEGORIES
            $this->getProductLink($row), // PRODUCT_URL
            $media->count(), // IMAGE_COUNT
            $mediaInfos->sizes, // IMAGE_SIZES
            $mediaInfos->size_sum, // IMAGE_SIZE_SUM
            $mediaInfos->urls, // IMAGE_LINKS
            $row->status === BasicStatus::Active ? '1' : '0', // PRODUCT_STATUS
            strip_tags($row->description), // PRODUCT_DESCRIPTION
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
       return Product::query()
            ->withoutGlobalScopes()
            ->with('brand');

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
    private function generateCategories(Product $product, bool $isMain = false)
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

    private function getProductLink($product)
    {
        return route('product.show', ['slug' => $product->slug]);
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

    private function getMediaInfos($media)
    {
        if (count($media)) {
            $sizes = []; //- Collecting sizes...
            $size_sum = 0; //- Summarized size of items...
            $urls = []; //- Collecting URLs...

            foreach ($media as $img) {
                $urls[] = $img->getUrl();
                $sizes[] = $img->file_name . ' (' . size_format($img->size) . ')';
                $size_sum += $img->size;
            }

            return (object)[
                'urls' => $urls,
                'sizes' => implode(';', $sizes), // IMAGE_SIZES
                'size_sum' => ($size_sum > 0) ? size_format($size_sum) : '', // IMAGE_SIZE_SUM
            ];
        }
        return (object)[
            'urls' =>null,
            'sizes' => null,
            'size_sum' =>null,
        ];
    }


    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $this->tracker->status = 'running';
                $this->tracker->save();

                Notification::make()
                    ->title('Exportálás folyamata...')
                    ->body(' Termékek exportálása...')
                    ->info()
                    ->sendToDatabase($this->tracker->exported_by);
            },

            // ExportFailed::class => function (ExportFailed $event)
            // {
            //   $this->tracker->addFail($event->getException()->getMessage());
            //   $this->tracker->status = 'failed';
            //   $this->tracker->save();
            //   $this->error($event->getException()->getMessage());
            // },

            AfterSheet::class => function (AfterSheet $event) {
                $this->tracker->status = 'finished';
                $this->tracker->finished_at = now();
                $this->tracker->save();

                Notification::make()
                    ->title('Exportálás folyamata...')
                    ->body((($this->tracker->fails_counter > 0) ? 'Végeztünk.' : 'Sikeresen végeztünk!') . ' A keresett állomány itt tölthető le: <a href="' . Storage::url($this->tracker->file) . '">' . $this->tracker->file . '</a>')
                    ->success()
                    ->sendToDatabase($this->tracker->exported_by);
            }
        ];
    }
}
