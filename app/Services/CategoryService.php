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
    $roots = Category::roots()
      ->orderBy('name', 'asc')
      ->get();

    if ($brand) { //- if brand set, we filter to select only categories which have products related to this brand.
      $roots = [];
      $categories = Category::onlyBrand($brand)
        ->orderBy('name', 'asc')
        ->get();
      foreach ($categories as $category) {
        $ancestors = $category->getAncestors();

        foreach ($ancestors as $ancestor) {
          if ($ancestor->isRoot() && !in_array($ancestor, $roots)) {
            $roots[] = $ancestor;
          }
        }
      }
      $roots = collect($roots);
    }

    return $roots; // ->sortBy('name');
  }

  public function findBySlug($slug): Category
  {
    $slugs = Str::of($slug)->explode('/');

    $category = Category::roots()
      ->where('slug', Arr::pull($slugs, 0))
      ->orderBy('name', 'asc')
      ->first();

    if (!$category) {
      abort(404);
    }

    foreach ($slugs as $slug_item) {
      $category = $category->children()
        ->where('slug', $slug_item)
        ->orderBy('name', 'asc')
        ->first();

      if (!$category) {
        abort(404);
      }
    }

    /** Getting category... */
    $this->category = $category;

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
    $children = $this->category->immediateDescendants()->get();

    if ($brand) {
      $children = [];
      $categories = Category::onlyBrand($brand)->get();
      foreach ($categories as $category) {
        $ancestors = $category->getAncestorsAndSelf()->sortBy('name');

        foreach ($ancestors as $ancestor) {
          if ($ancestor->parent_id == $this->category->id && !in_array($ancestor, $children)) {
            $children[] = $ancestor;
          }
        }
      }
      $children = collect($children);
    }

    return $children->sortBy('name');
  }

  public function getProducts(Brand $brand = null)
  {
    $products = $this->category->products()->orderBy('slug', 'asc');

    if ($brand) {
      $products->onlyBrand($brand);
    }

    return $products->paginate($this->paginate);
  }

  public function path($slug = null)
  {
    $path   = array();

    if ($slug) {
      request()->session()->flash('path', $slug);

      $slugs  = Str::of($slug)->explode('/');

      $category = Category::roots()
        ->where('slug', Arr::pull($slugs, 0))
        ->first();

      $path[] = $category;

      foreach ($slugs as $slug_item) {
        $category = $category->children()->where('slug', $slug_item)
          ->first();

        $path[] = $category;
      }
    }

    return $path;
  }

  public function getMainSlug(Product $product): string
  {
    $slugs = [];
    $categories = $product->categories()->where('is_main', true)->first()?->getAncestorsAndSelf();
    if ($categories) {
      foreach ($categories as $category) {
        $slugs[] = $category->slug;
      }
    }

    return Arr::join($slugs, '/');
  }

  public function getMainSlugIfNotValid(Product $product, string $slug): string
  {
    $category = $this->findBySlug($slug);

    if (!$category->products()->where("category_product.product_id", "=", $product->id)->exists())
    {
      $slug = $this->getMainSlug($product);
    }

    return $slug;
  }
}
