<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Baum\Extensions\Eloquent\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class ProductService
{
  protected $product;

  public function __construct()
  {
    //...
  }

  public function get($slug)
  {
    $this->product = Product::where('slug', $slug)
      ->with('brand')
      ->firstOrFail();

    return $this->product;
  }
}
