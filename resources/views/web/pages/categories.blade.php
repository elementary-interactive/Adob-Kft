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

  <!-- categories -->

  <div class="container-fluid">
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
