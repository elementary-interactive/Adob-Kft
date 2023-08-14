<?php

namespace App\Console\Commands;

use App\Models\BrandCategoryCounter;
use App\Models\BrandCategoryCounts;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\Category;
use App\Models\CategoryProduct;
use Neon\Models\Statuses\BasicStatus;

class CalculateProductCounts extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:calculate-product-counts';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Calculating product counts by brands for categories.';

  /**
   * Execute the console command.
   */
  public function handle()
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

    $this->info('Counters updated successfully!');
  }
}
