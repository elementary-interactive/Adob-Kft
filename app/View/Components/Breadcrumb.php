<?php

namespace App\View\Components;

use App\Models\Brand;
use App\Models\Category;
use App\Services\CategoryService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    protected $category;
    protected $brand;

    protected $is_product = false;
   
    /**
     * Create a new component instance.
     */
    public function __construct(
        CategoryService $service,
        protected string $slug,
        bool $product = false
    )
    {
        $this->is_product   = $product;

        $this->category = request()->session()->get('category'); //- Category::find($category_id)->first();
        $this->brand = request()->session()->get('brand'); //- Brand::find($brand_id)->first();
        //...
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.breadcrumb', [
            'path'          => $this->category->getAncestors(),
            'current'       => $this->category,
            'brand'         => $this->brand,
            'is_product'    => $this->is_product,
            'slug'          => route('product.browse', [
                'slug'  => null
            ])
        ]);
    }
}
