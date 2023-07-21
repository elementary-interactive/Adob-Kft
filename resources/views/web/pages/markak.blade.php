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

@section('body')

<!-- highlight brands -->
@if ($featured?->count())
<div class="container-fluid fix-width highlight-brands">
  <div class="row">
    @foreach ($featured as $featured_item)
    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
      <a href="{{ route('brands.browse', ['brand' => $featured_item->slug]) }}">
      <div class="adob-brand_item" role="button">
        <img src="" alt="">
        <h3>{{ $featured_item->name }}</h3>
      </div>
      </a>
    </div>
    @endforeach
  </div>
</div>
@endif
<!-- categories -->
@if ($brands?->count())
<div class="container-fluid fix-width">
  <div class="row">
    @foreach ($brands as $brand_item)
    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
      <a href="{{ route('brands.browse', ['brand' => $brand_item->slug]) }}">
      <div class="adob-cat_item" role="button">
        <h3>{{ $brand_item->name }}</h3>
        {{-- <span>3 termék</span> --}}
      </div>
      </a>
    </div>
    @endforeach
  </div>
</div>
@endif
@endsection
