@extends('web.layouts.default')

@section('title', __('meta.impressum.title'))

@push('og')
  @include('web.layouts.head.og', ['og' => [
      'title'             => __('meta.impressum.title'),
      'description'       =>  __('meta.impressum.description'),
      'type'              => 'info-page',
      'url'               => \Request::url()
  ]])
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
              <li class="splide__slide">
                <img src="{{ Vite::asset('/resources/images/products/01.jpg') }}" alt="">
              </li>
              <li class="splide__slide">
                <img src="{{ Vite::asset('/resources/images/products/02.jpg') }}" alt="">
              </li>
              <li class="splide__slide">
                <img src="{{ Vite::asset('/resources/images/products/02.jpg') }}" alt="">
              </li>
            </ul>
          </div>
        </section>
        <ul id="thumbnails" class="thumbnails">
          <li class="thumbnail">
            <img src="{{ Vite::asset('/resources/images/products/01.jpg') }}" alt="">
          </li>
          <li class="thumbnail">
            <img src="{{ Vite::asset('/resources/images/products/02.jpg') }}" alt="">
          </li>
          <li class="thumbnail">
            <img src="{{ Vite::asset('/resources/images/products/02.jpg') }}" alt="">
          </li>
        </ul>
      </div>
      <div class="col-12 col-xl-6 product-infos">
        <h2 class="blue">Füldugó TWS szabadkezes HEADSET BlueTooth hordozóval, fekete GRUNDIG</h2>
        <div class="row">
          <div class="col-md-12">
            <dl>
              <dt>Márka</dt>
              <dd>ALLRIDE</dd>
            </dl>
            <dl>
              <dt>Cikkszám</dt>
              <dd>XEH25222733</dd>
            </dl>
            <dl>
              <dt>EAN</dt>
              <dd>871125222733</dd>
            </dl>
            <dl>
              <dt>Ár</dt>
              <dd>3487</dd>
            </dl>
          </div>
        </div>
        <div class="caption">
          <p class="text-justify">Súly: 0,875Kg. 280x280x90mm. 12db/karton. EAN13: 8711252227337</p>
        </div>
      </div>
    </div>
  </div>

@endsection
