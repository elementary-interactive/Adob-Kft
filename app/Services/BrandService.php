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
  public $brand   = null;

  public function __construct()
  {
    //...
  }

  public function init($slug = null, $paginate = null)
  {
  }

  public function getFeaturedBrands()
  {
    return Brand::where('is_featured', 1)->get();
  }

  public function getBrands()
  {
    return Brand::where('is_featured', 0)->get();
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

    return $this->brand;
  }
}
