<?php

namespace App\Services;

use App\Models\Brand;
use Baum\Extensions\Eloquent\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class BrandService
{
  /** The selected brand if found.
   * @var Brand|null
   */
  public $brand     = null;

  public $paginate  = 25;

  public function __construct()
  {
    //...
  }

  public function init($slug = null, $paginate = null)
  {
  }

  public function getFeaturedBrands()
  {
    return Brand::where('is_featured', 1)->orderBy('name', 'asc')->get();
  }

  public function getBrands()
  {
    return Brand::where('is_featured', 0)->orderBy('name', 'asc')->get();
  }

  public function find($id)
  {
    $this->brand = Brand::find($id)
      ->first();
   
    return $this->brand;
  }
  
  public function findBySlug($slug): Brand
  {
    /** Getting category... */
    $this->brand = Brand::where('slug', $slug)
      ->firstOrFail();

      request()->session()->flash('brand', $this->brand->id);

    return $this->brand;
  }

  public function getProducts() 
  {
      $products = $this->brand->products();
  
      return $products
        ->orderBy('product_id', 'ASC')
        ->paginate($this->paginate)
        ->withQueryString();
    }
  }
}
