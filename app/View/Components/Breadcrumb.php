<?php

namespace App\View\Components;

use App\Models\Category;
use App\Services\CategoryService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    protected $service;
   
    /**
     * Create a new component instance.
     */
    public function __construct(
        CategoryService $service,
        protected string $slug
    )
    {
        $this->service = $service;
        $this->service->init($slug);
        //...
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.breadcrumb', [
            'path'      => $this->service->category->getAncestors(),
            'current'   => $this->service->category,
            'slug'      => route('product.browse', [
                'slug'  => null
            ])
        ]);
    }
}
