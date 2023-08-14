<?php

namespace App\Http\Controllers;

use App\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

use Neon\Services\LinkService;
use App\Services\CategoryService;
use Neon\Models\Link;

class CategoryController extends Controller
{
    protected $brand_service;
    protected $category_service;

    public function __construct(BrandService $brand_service, CategoryService $category_service)
    {
        $this->brand_service    = $brand_service;
        $this->category_service = $category_service;
    }

    public function browse(LinkService $page_service, Request $request, string $slug = null)
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

        /** Getting site config's paginate value.
         * 
         * @var int
         */
        $this->category_service->paginate = site()->product_paginate;

        if ($slug)
        {
            $category   = $this->category_service->findBySlug($slug);

            if ($category)
            {
                $products   = $this->category_service->getProducts();
                $categories = $this->category_service->getChildren();
            }
        }
        
        if (!$categories)
        {
            $categories = $this->category_service->root();
        }

        return View::first(
            $page_service->getViews(Arr::first(site()->domains)),
            [
                'page'       => $page,
                'category'   => $category,
                'categories' => $categories,
                'products'   => $products,
                'path'       => $this->category_service->path($slug),
            ]
        );
    }
}
