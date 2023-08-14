<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Baum\Extensions\Eloquent\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Laravel\Nova\Fields\Slug;

class CategoryService
{
  /** The selected category if found.
   * @var Category|null
   */
  public $category    = null;

  /** Collection of Products.
   * @var EloquentCollection|null
   */
  public $products    = null;

  public $paginate    = 15;

  public function __construct()
  {
    //...
  }

  public function root(Brand $brand = null)
  {
    /** Getting roots...
     * 
     */
    $roots = Category::roots();

    if ($brand) { //- if brand set, we filter to select only categories which have products related to this brand.
      $roots->onlyBrand($brand);
    }

    return $roots->get();
  }

  public function findBySlug($slug): Category
  {
    $slugs = Str::of($slug)->explode('/');

    if ($slugs->count() == 1)
    {
      $category = Category::roots()
        ->where('slug', Arr::pull($slugs, 0))
        ->first()
        ->getDescendantsAndSelf();
    } else {
      $category = Category::roots()
        ->where('slug', Arr::pull($slugs, 0))
        ->first()
        ->getDescendants();
    }
   
    foreach ($slugs as $slug_item)
    {
      $category = $category->where('slug', $slug_item)
        ->first()
        ->getDescendantsAndSelf();
    }

    /** Getting category... */
    $this->category = $category->first();

    return $this->category;
  }

  public function find($id): Category
  {
    $this->category = Category::find($id)
      ->first();

    return $this->category;
  }

  public function getChildren(Brand $brand = null)
  {
    $children = $this->category->descendants()->withCount('products');

    if ($brand)
    {
      $children->onlyBrand($brand);
    }

    return $children->get();
  }

  public function getProducts(Brand $brand = null)
  {
    $products = $this->category->products();

    if ($brand)
    {
      $products->onlyBrand($brand);
    }

    return $products->paginate($this->paginate);
  }

  public function path($slug = null)
  {
    $path   = array();

    if ($slug)
    {
      request()->session()->flash('path', $slug);
    
      $slugs  = Str::of($slug)->explode('/');

      $category = Category::roots()
        ->where('slug', Arr::pull($slugs, 0))
        ->first()
        ->getDescendantsAndSelf();

      $path[] = $category->first();
     
      foreach ($slugs as $slug_item)
      {
        $category = $category->where('slug', $slug_item)
          ->first()
          ->getDescendantsAndSelf();

        $path[] = $category->first();
      }
    }

    return $path;
  }
}