@extends('web.layouts.default')

@section('title', $page->title)

@push('meta')
  @include('web.layouts.head.meta', ['meta' => [
      'title'             => $page->meta_title,
      'description'       => $page->meta_description,
      'keywords'          => $page->meta_keywords
  ]])
@endpush

@push('og')
  @include('web.layouts.head.og', ['og' => [
      'title'             => $page->og_title,
      'description'       => $page->og_description,
      'type'              => 'info-page',
      'url'               => \Request::url()
  ]])
@endpush


@section('body')

{!! $page->view !!}

@endsection
