<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Logtail\Monolog\LogtailHandler;
use Monolog\Logger;
use Neon\Models\Statuses\BasicStatus;

class CountBrandCategoryProducts implements ShouldQueue
{
  use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $logger;

  /**
   * Create a new job instance.
   */
  public function __construct()
  {
    $this->logger = new Logger('adob_importer');
    $this->logger->pushHandler(new LogtailHandler('1sKmnmxToqZ5NPAJy6EfvyAZ'));
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $this->logger->info('Brand category count...');

    try {
      DB::beginTransaction();
      
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
        ->map(fn ($value) => (array) $value) // Make all items to array.
        ->toArray();

      DB::table('brand_category_counts')->insertOrIgnore($data);

      DB::commit();

      $this->logger->info('Brand category count done.');

      //   $this->info('Counters updated successfully!');
    } catch (\Throwable $e) {
      // DB::rollback();

      $this->logger->error('Brand category count error: '.$e->getMessage());
      //   $this->error('Fuck.');
    }
  }
}
