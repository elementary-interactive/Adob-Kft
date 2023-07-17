<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

use Neon\Services\LinkService;
use App\Services\CategoryService;
use Neon\Models\Link;

class CategoryController extends Controller
{
    protected $category_service;

    public function __construct(CategoryService $category_service)
    {
        $this->category_service = $category_service;
    }

    public function browse(LinkService $page_service, Request $request, string $slug = null)
    {
        // \DB::enableQueryLog();
        // echo "here we go";

        /** Geting the current page.
         * 
         * @var  Link $page
         */
        $page       = $page_service->find('termekek');

        /** Initalize category service.
         * 
         */
        $this->category_service->init($slug, 5); //- $page->product_paginate);

        return View::first(
            $page_service->getViews(Arr::first(app('site')->current()->domains)),
            [
                'page'       => $page,
                'category'   => $this->category_service->category,
                'categories' => $this->category_service->categories,
                'products'   => $this->category_service->products
            ]
        );
    }
}
