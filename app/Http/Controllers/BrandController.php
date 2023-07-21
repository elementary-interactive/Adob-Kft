<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Services\BrandService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

use Neon\Services\LinkService;
use Neon\Models\Link;

class BrandController extends Controller
{
    protected $brand_service;
    protected $category_service;

    public function __construct(BrandService $brand_service, CategoryService $category_service)
    {
        $this->brand_service    = $brand_service;
        $this->category_service = $category_service;
    }

    public function brands(LinkService $page_service, Request $request)
    {
        // \DB::enableQueryLog();
        /** Geting the current page.
         * 
         * @var  Link $page
         */
        $page       = $page_service->find('markak');

        return View::first(
            $page_service->getViews(Arr::first(site()->domains)),
            [
                'page'       => $page,
                'featured'   => $this->brand_service->getFeaturedBrands(),
                'brands'     => $this->brand_service->getBrands(),
            ]
        );

    }

    public function browse(LinkService $page_service, Request $request, string $brand, string $slug = null)
    {
        /** We need these:
         * 
         */
        $category   = null;
        $products   = null;
        $categories = null;

        /** Geting the current page.
         * 
         * @var  Link $page
         */
        $page       = $page_service->find('termekek');

        /** Get the Brand from the URL...
         * 
         * @var Brand
         */
        $brand      = $this->brand_service->findBySlug($brand);

        /** Getting site config's paginate value.
         * 
         * @var int
         */
        $this->category_service->paginate = site()->product_paginate;

        if ($slug)
        {
            $category   = $this->category_service->findBySlug(Arr::last(explode('/', $slug)));
            if ($category)
            {
                $products   = $this->category_service->getProducts($brand);
                $categories = $this->category_service->getChildren($brand);
            }
        }
        
        if (!$categories)
        {
            $categories = $this->category_service->root($brand);
        }

        return View::first(
            $page_service->getViews(Arr::first(site()->domains)),
            [
                'page'       => $page,
                'brand'      => $brand,
                'category'   => $category,
                'categories' => $categories,
                'products'   => $products,
                'path'       => $this->category_service->path($slug),
            ]
        );
    }
}
