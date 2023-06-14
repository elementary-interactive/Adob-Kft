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

  <!-- search -->

  <div class="container-fluid search-container">
    <div class="row">
      <div class="col-12">
        <div class="accordion" id="accordionExample">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                aria-expanded="true" aria-controls="collapseOne">
                38 termék találat
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <div class="container-fluid">
                  <div class="row">
                    <div class="col-12">
                      
                      @include('web.pages.partials.productlist')

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection
