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

  <!-- highlight brands -->

  <div class="container-fluid fix-width highlight-brands">
    <div class="row">
      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-brand_item" role="button">
          <img src="{{ Vite::asset('/resources/images/brand/tungsram.png') }}" alt="">
          <h3>TUNGSRAM</h3>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-brand_item" role="button">
          <img src="{{ Vite::asset('/resources/images/brand/eta.png') }}" alt="">
          <h3>ETA</h3>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-brand_item" role="button">
          <img src="{{ Vite::asset('/resources/images/brand/gogen.webp') }}" alt="">
          <h3>GOGEN</h3>
        </div>
      </div>

    </div>
  </div>

  <!-- categories -->

  <div class="container-fluid fix-width">
    <div class="row">
      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

    </div>
  </div>

@endsection
