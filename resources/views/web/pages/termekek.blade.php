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
    @push('breadcrumb')
        <x-breadcrumb slug="{{ $category?->slug }}" />
    @endpush
@endif

@section('body')
    @if (isset($categories) && $categories->count())
        <div class="container-fluid">
            <div class="row">
                @foreach ($categories as $category_item)
                    @if ($category_item->counts)
                        @include ('partials.browser.item', [
                            'item' => $category_item,
                        ])
                    @endif
                @endforeach
            </div>
        </div>
    @endif
    @if (isset($products) && $products->count())
        <div class="container-fluid fix-width">
            <div class="row">
                <div class="col-12">

                    @include('web.pages.partials.productlist', [
                        'products' => $products,
                    ])

                    @include('web.pages.partials.pagination', [
                        'pager' => $products,
                    ])

                </div>
            </div>
        </div>
    @endif
@endsection
