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

  <!-- about -->

  <div class="container-fluid fix-width pricelists-container">
    <div class="row">
      <div class="col-12">
        <h1>Rólunk</h1>
        <p>Az Adob Kft. csak nagykereskedelmi tevékenységet végez, magánszemélyeket nem szolgálunk ki.</p>

        <ul>
          <li>Weben keresztül nem történik értékesítés.</li>
          <li>A feltüntetett árak csak jelzés értékűek, tényleges árakat személyes ajánlatban küldünk.</li>
          <li>A cikkszámhoz tartozó web-cím szövege az adott termék jellegére vonatkozóan tartalommal nem bír.</li>
          <li>A képek és termékinformációk csak tájékoztató jellegűek. Az esetleges eltérésekért és hibákért az Adob
            Kft. nem vállal felelősséget. </li>
        </ul>

        <h2>Cookie nyilatkozat</h2>
        <p>Cookie-kat nem tárolunk.</p>

        <h2>Kapcsolat</h2>
        <p>email: kapcsolat [kukac] adob.hu</p>
        <p>mobil: +36 30 110 6126</p>
      </div>
    </div>
  </div>

@endsection
