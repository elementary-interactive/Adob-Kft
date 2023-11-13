{{--@if(config('site')->id == 'c6938d50-4261-40e3-aaa8-84e523ebe7a0')
    @extends('relaxsos.layouts.default')
@else 
    @extends('web.layouts.default')
@endif--}}
@extends('web.layouts.default')
@section('body')

  <section class="main-container">

    <div class="attention-container">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="attention-content">
              <h1>404</h1>
              <h2 class="white uppercase">404</h2>
              <a href="{{route('index')}}" class="defbtn transp">Kezdőoldal &rarr;</a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>

@endsection