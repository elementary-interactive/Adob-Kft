<?php

namespace App\Services;

use App\Models\Category;
use Baum\Extensions\Eloquent\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class CategoryService
{
  /** The selected category if found.
   * @var Category|null
   */
  public $category    = null;

  /** Collection of Category models.
   * @var Collection|null
   */
  public $categories  = null;

  /** Collection of Products.
   * @var EloquentCollection|null
   */
  public $products    = null;

  protected $paginate = 15;

  public function __construct()
  {
    //...
  }

  public function init($slug = null, $paginate = null)
  {
    if ($paginate)
    {
      $this->paginate = $paginate;
    }

    if ($slug)
    {
      /** Getting category... */
      $this->category = Category::where('slug', Arr::last(explode('/', $slug)))
        ->first();

      if ($this->category) {
        request()->session()->put('category_id', $this->category->id);
      }

      /** Getting list of categories. */
      $this->categories = Category::where('slug', Arr::last(explode('/', $slug)))
        ->first()
        ->descendants()
        ->get();

      /** Getting products of the selected category. */
      $this->products = $this->category->products()->paginate($this->paginate);
    } else {
      /** If no 'slug' given, getting the root categries. */
      $this->categories = Category::roots()->get();
    }
  }

  public function find($id)
  {
    return Category::find($id)
      ->first();
  }
}
