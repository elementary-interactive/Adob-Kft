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

  public function getMainCategory(): Category
  {
    return $this->product->categories()->where('is_main', true)->first();
  }

  public function siblings($path)
  {
    $current = last($path);
    $products = $current->products()->orderByPivot('order', 'asc')->orderBy('products.slug', 'asc')->get(['slug']);
    $prev = null;
    $next = null;
    $order = 0;

    if ($products->count())
    {
      foreach ($products as $key => $product)
      {
        if ($this->product->slug == $product->slug)
        {
          $order = $key;
        }
      }
      $prev = $products->slice(0, $order)->last();
      
      if ($products->slice($order, 2)->count() > 1)
      {
        $next = $products->slice($order, 2)->last();
      }
    }

    return [
      'prev'  => ($prev) ? $prev->slug : null,
      'next'  => ($next) ? $next->slug : null
    ];
  }
}
