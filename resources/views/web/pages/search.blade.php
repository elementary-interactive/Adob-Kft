@extends('web.layouts.default')

@section('title', $page->title.': '.$search_term)

@push('og')
  @include('web.layouts.head.og', ['og' => [
      'title'             => $page->title.': '.$search_term,
      'description'       =>  __('meta.impressum.description'),
      'type'              => 'info-page',
      'url'               => \Request::url()
  ]])
@endpush


@section('body')

  <!-- search -->

  <div class="container-fluid search-container">
    <div class="row">
      <div class="col-12">
        <div class="page-header"><h1>{{ $page->title }}: <small>{{ $search_term }}</small></h1></div>
        <div class="accordion" id="accordionExample">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                aria-expanded="true" aria-controls="collapseOne">
                {{ $search_result->total() }} termék találat
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <div class="container-fluid">
                  @if ($search_result->count())
                  <div class="row">
                    <div class="col-12">
                      
                      @include('web.pages.partials.productlist', [
                        'products'    => $search_result,
                        'search_term' => $search_term
                      ])

                      @include('web.pages.partials.pagination', [
                        'pager' => $search_result,
                      ])

                    </div>
                  </div>
                  @else
                  <div class="row">
                    <div class="col-12" style="padding-bottom: 3rem;">
                      Sajnos, nem találtunk semmit.
                    </div>
                  </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
