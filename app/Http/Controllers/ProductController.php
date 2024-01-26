<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

use Neon\Services\LinkService;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Models\Category;
use App\Models\Product;
use Neon\Models\Link;


class ProductController extends Controller
{
    protected ProductService $product_service;

    protected CategoryService $category_service;

    public function __construct(ProductService $product_service, CategoryService $category_service)
    {
        $this->product_service  = $product_service;
        $this->category_service = $category_service;
    }

    public function addImage(Request $request)
    {
        /**
         * @var Product $product
         */
        $product = Product::withoutGlobalScopes()->withoutTrashed()->find($request->input('product_id'));
        
        if ($request->hasFile('file'))
        {
            $product->addMediaFromRequest('file')
                ->toMediaCollection($product::MEDIA_COLLECTION);
        }
        if ($request->has('url'))
        {
            $product->addMediaFromUrl($request->input('url'))
                ->toMediaCollection($product::MEDIA_COLLECTION);
        }

        return response()->json($product->getMedia($product::MEDIA_COLLECTION)->toArray(), 200);
    }

    public function show(LinkService $page_service, Request $request, string $slug)
    {
        /** Creating a static pseudo page.
         * 
         * @var  Link $page
         */
        $page       = $page_service->static('termek');
        $product    = $this->product_service->get($slug);
        $brand      = null;
        $category   = null;
        $path       = [];
        
        if (request()->session()->has('path'))
        {
            /** 
             * @var string
            */
            $slugs = request()->session()->get('path');
        } else {
            $slugs = $this->category_service->getMainSlug($product);
        }
        $category   = $this->category_service->findBySlug($slugs);
        $path       = $this->category_service->path($slugs);

        $siblings   = $this->product_service->siblings($path);
        // $product_prev = $this->product_service->prev($path);
        // /** Initalize category service.
        //  * 
        //  */
        // $this->category_service->init($slug, 5); //- $page->product_paginate);
        return View::first(
            $page_service->getViews(Arr::first(app('site')->current()->domains)),
            [
                'page'              => $page,
                'product'           => $product,
                'brand'             => $brand,
                'category'          => $category,
                'path'              => $path,
                'product_slug_prev' => $siblings['prev'],
                'product_slug_next' => $siblings['next']
            ]
        );
    }


    public function media(LinkService $page_service, Request $request, string $slug)
    {
        \DB::enableQueryLog();
        /** Creating a static pseudo page.
         * 
         * @var  Link $page
         */
        $page       = $page_service->static('termek');

        $product    = $this->product_service->get($slug);

        $x = $product->addMedia(storage_path('242937646_10161267560773012_507480216691950664_n.jpg'))->toMediaCollection(Product::MEDIA_MAIN);
        $m = $product->getMedia(Product::MEDIA_COLLECTION);
        $m[0]->getUrl('thumb');
        $l = \DB::getQueryLog();

        dd($m, $l, $m[0]->getUrl('thumb'));
        
        // /** Initalize category service.
        //  * 
        //  */
        // $this->category_service->init($slug, 5); //- $page->product_paginate);
        return View::first(
            $page_service->getViews(Arr::first(app('site')->current()->domains)),
            [
                'page'       => $page,
                'product'    => $product
            ]
        );
    }
}
