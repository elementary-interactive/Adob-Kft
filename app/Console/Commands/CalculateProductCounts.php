<?php

namespace App\Console\Commands;

use App\Jobs\CountBrandCategoryProducts;
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
    CountBrandCategoryProducts::dispatch();

    $this->info('Job started.');

    return true;
  }
}
