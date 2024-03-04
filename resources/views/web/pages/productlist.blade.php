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

  <!-- productlist -->

  <div class="container-fluid fix-width">
    <div class="row">
      <div class="col-12">
        
        @include('web.pages.partials.productlist', ['search_term' => null])

        {{-- @include('web.pages.partials.pagination') --}}

      </div>
    </div>
  </div>

@endsection
