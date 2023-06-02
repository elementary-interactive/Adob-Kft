<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>@yield('title')</title>
        @include('web.layouts.head.head')
    </head>
    <body id="page-top">
        {{--
        @if (config('app.env') == 'production' && config('google.gtm-code'))
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ config('google.gtm-code') }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        @endif
        <a href="#page-top" id="topBtn"><span class="arrow-up"></span></a>
        --}}

        @include('web.layouts.header.header')

        @section('body')
        @show

        @include('web.layouts.footer.footer')
        @include('web.layouts.footer.scripts')
    </body>
</html>
