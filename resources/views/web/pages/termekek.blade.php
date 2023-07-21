@extends('web.layouts.default')

@section('title', $page->title)

@push('meta')
    @include('web.layouts.head.meta', [
        'meta' => [
            'title' => $page->meta_title,
            'description' => $page->meta_description,
            'keywords' => $page->meta_keywords,
        ],
    ])
@endpush

@push('og')
    @include('web.layouts.head.og', [
        'og' => [
            'title' => $page->og_title,
            'description' => $page->og_description,
            'type' => 'info-page',
            'url' => \Request::url(),
        ],
    ])
@endpush

@if (isset($category))
    @include('components.breadcrumb', [
        'brand' => (isset($brand)) ? $brand : null,
        'path'  => $path,
    ])
@endif

@section('body')
    @include('web.pages.partials.categories', [
        'route'         => (isset($brand)) ? 'brands.browse' : 'product.browse',
        'brand'         => (isset($brand)) ? $brand : null,
        'categories'    => $categories
    ])
    @include('web.pages.partials.products', [
        'products'      => $products
    ])
@endsection
