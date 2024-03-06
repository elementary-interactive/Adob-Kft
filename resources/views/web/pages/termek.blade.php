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
            <div class="col-12 col-xl-6 col-md-6 col-sm-6">
                @if ($product->getMedia(\App\Models\Product::MEDIA_COLLECTION)->count())
                    <section id="main-carousel" class="splide">
                        <div class="splide__track">
                            <ul class="splide__list">
                                @forelse ($product->getMedia(\App\Models\Product::MEDIA_COLLECTION)->sort() as $media)
                                    <li class="splide__slide">
                                        {{-- <img src="{{ $media->getUrl('medium') }}" height="600" alt=""> --}}
                                        {{ $media }}
                                    </li>
                                @empty
                                    <li class="splide__slide">
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </section>
                    @if ($product->getMedia(\App\Models\Product::MEDIA_COLLECTION)->count() > 1)
                    <ul id="thumbnails" class="thumbnails">
                        @forelse ($product->getMedia(\App\Models\Product::MEDIA_COLLECTION) as $media)
                            <li class="thumbnail">
                                <img src="{{ $media->getUrl('thumb') }}" height="100" alt="">
                            </li>
                        @empty
                            <li class="thumbnail">
                            </li>
                        @endforelse
                    </ul>
                    @endif
                @endif
            </div>
            <div class="col-12 col-xl-6 col-md-6 col-sm-6 product-infos">
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

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js">
    </script>

<script type="text/javascript">

var splide = new Splide('#main-carousel', {
    pagination: false,
    arrows: {{ $product->getMedia(\App\Models\Product::MEDIA_COLLECTION)->count() > 1 ? 'true' : 'false' }},
    rewind: true,
    gap: 30,
});

var thumbnails = document.getElementsByClassName('thumbnail');
var current;

for (var i = 0; i < thumbnails.length; i++) {
    initThumbnail(thumbnails[i], i);
}

function initThumbnail(thumbnail, index) {
    thumbnail.addEventListener('click', function () {
        splide.go(index);
    });
}

splide.on('mounted move', function () {
    var thumbnail = thumbnails[splide.index];

    if (thumbnail) {
        if (current) {
            current.classList.remove('is-active');
        }

        thumbnail.classList.add('is-active');
        current = thumbnail;
    }
});

splide.mount();
</script>
@endpush