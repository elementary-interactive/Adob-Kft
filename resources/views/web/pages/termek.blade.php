@extends('web.layouts.default')

@section('title', $product->name . ' | ADOB')
@push('og')
    @include('web.layouts.head.og', [
        'og' => [
            'title' => $product->name,
            'description' => $product->description,
            'image' => '',
            'type' => 'website',
            'url' => \Request::url(),
        ],
    ])
@endpush

@push('meta')
    @include('web.layouts.head.meta', [
        'meta' => [
            'title' => $product->name,
            'description' => $product->description,
            // 'image'             => '',
            // 'type'              => 'website',
            // 'url'               => \Request::url()
        ],
    ])
@endpush

@push('breadcrumb')
    @include('components.breadcrumb', [
        'path' => $path,
        'brand' => isset($brand) ? $brand : null,
        'is_product' => true,
    ])
@endpush

@section('body')
    <!-- product -->

    <div class="container-fluid product-container">
        <div class="row">
            <div class="col-12 col-xl-6">
                <section id="main-carousel" class="splide"
                    aria-label="The carousel with thumbnails. Selecting a thumbnail will change the Beautiful Gallery carousel.">
                    <div class="splide__track">
                        <ul class="splide__list">
                            @forelse ($product->getMedia(\App\Models\Product::MEDIA_COLLECTION) as $media)
                                <li class="splide__slide">
                                    <img src="{{ $media->getUrl('medium') }}" alt="">
                                </li>
                            @empty
                                <li class="splide__slide">
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </section>
                <ul id="thumbnails" class="thumbnails">
                    @forelse ($product->getMedia(\App\Models\Product::MEDIA_COLLECTION) as $media)
                        <li class="thumbnail">
                            <img src="{{ $media->getUrl('thumb') }}" alt="">
                        </li>
                    @empty
                        <li class="thumbnail">
                        </li>
                    @endforelse
                </ul>
            </div>
            <div class="col-12 col-xl-6 product-infos">
                <h2 class="blue">{{ $product->name }}</h2>
                <div class="row">
                    <div class="col-md-12">
                        <dl>
                            <dt>Márka</dt>
                            <dd>{{ $product->brand?->name }}</dd>
                        </dl>
                        <dl>
                            <dt>Cikkszám</dt>
                            <dd>{{ $product->product_id }}</dd>
                        </dl>
                        <dl>
                            <dt>EAN</dt>
                            <dd>{{ $product->ean }}</dd>
                        </dl>
                        @if ($product->price)
                            <dl>
                                <dt>Ár</dt>
                                <dd>{{ $product->price }},- Ft
                                    @if ($product->on_sale)
                                        &mdash; Jelenleg akciós!
                                    @endif
                                </dd>
                            </dl>
                        @endif

                    </div>
                </div>
                <div class="caption">
                    <p class="text-justify">{!! $product->description !!}<br />{!! $product->packaging !!}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="pagination">
                @if ($product_slug_prev)
                <a href="{{ route('product.show', ['slug' => $product_slug_prev]) }}" class="prev"><i class="icon-left-open"></i></a>
                @endif
                @if ($product_slug_next)
                <a href="{{ route('product.show', ['slug' => $product_slug_next]) }}" class="next"><i class="icon-right-open"></i></a>
                @endif
            </div>
        </div>
    </div>

@endsection
