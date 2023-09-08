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
    $products = $current->products()->withPivot('order')->orderBy('order')->get();
    $prev = null;
    $next = null;
    $order = 0;

    foreach ($products as $product)
    {
      if ($this->product->id == $product->id)
      {
        $order = $product->pivot->order;
      }
    }
    $prev = $products->filter(function($product) use ($order) {
      return $product->pivot->order < $order;
    })->last();
   
    $next = $products->filter(function($product) use ($order) {
      return $product->pivot->order > $order;
    })->first();
    
    return [
      'prev'  => ($prev) ? $prev->slug : null,
      'next'  => ($next) ? $next->slug : null
    ];
  }
}
