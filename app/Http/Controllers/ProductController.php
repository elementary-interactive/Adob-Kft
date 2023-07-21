<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

use Neon\Services\LinkService;
use App\Services\CategoryService;
use App\Services\ProductService;
use Neon\Models\Link;

class ProductController extends Controller
{
    protected ProductService $product_service;

    protected CategoryService $category_service;

    public function __construct(ProductService $product_service, CategoryService $category_service)
    {
        $this->product_service = $product_service;
        $this->category_service = $category_service;
    }

    public function show(LinkService $page_service, Request $request, string $slug)
    {
        /** Creating a static pseudo page.
         * 
         * @var  Link $page
         */
        $page       = $page_service->static('termek');

        // /** Initalize category service.
        //  * 
        //  */
        // $this->category_service->init($slug, 5); //- $page->product_paginate);
        return View::first(
            $page_service->getViews(Arr::first(app('site')->current()->domains)),
            [
                'page'       => $page,
                'product'    => $this->product_service->get($slug),
                'category'   => $this->category_service->find($request->session()->get('category')->id),
                'path'       => $this->category_service->path(null),
                // 'categories' => $this->category_service->categories,
                // 'products'   => $this->category_service->products
            ]
        );
    }
}
