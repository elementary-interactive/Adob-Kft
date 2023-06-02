@extends('web.layouts.default')

@section('body')
  <section class="main-container">
      <div class="attention-container">
          <div class="container-fluid">
              <div class="row">
                  <div class="col-12">
                      <div class="attention-content">
                          <h1>500</h1>
                          <h2 class="white uppercase">{{ __('errors.500') }}</h2>
                          <a href="" class="defbtn transp">{!! __('generic.back') !!}</a>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </section>
@endsection
