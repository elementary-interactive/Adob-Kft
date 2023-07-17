<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="format-detection" content="telephone=no">
<meta name="viewport" content="width=device-width, initial-scale=1">
{{--
    Include meta tags.
--}}
@stack('meta')
{{--
    Include OpenGraph related elements.
--}}
@stack('og')

{{--
    Favicon by Neon Site. (If Neon Site is set, please uncomment.)
--}}
<x-neon-favicon/>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap"
rel="stylesheet">
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/@mdi/light-font@0.2.63/css/materialdesignicons-light.min.css">

@vite(['resources/scss/app.scss', 'resources/js/app.js', 'resources/js/splide.min.js', 'resources/js/site.js'])

{{--
@if (config('app.env') == 'production' && config('google.gtm-code'))
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ config('google.gtm-code') }}');</script>
    <!-- End Google Tag Manager -->
@endif

@if (config('app.env') == 'production' && config('facebook.pixel-id'))
<!-- Meta Pixel Code --> <script> !function(f,b,e,v,n,t,s) {if(f.fbq)return;n=f.fbq=function(){n.callMethod? n.callMethod.apply(n,arguments):n.queue.push(arguments)}; if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0'; n.queue=[];t=b.createElement(e);t.async=!0; t.src=v;s=b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t,s)}(window, document,'script', 'https://connect.facebook.net/en_US/fbevents.js'); fbq('init', '{{ config('facebook.pixel-id') }}'); fbq('track', 'PageView'); </script> <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ config('facebook.pixel-id') }}&ev=PageView&noscript=1" /></noscript> <!-- End Meta Pixel Code -->
@endif


<script>
    var cookie_statement = "{{ route('document', ['slug' => app('site')->current()?->document_cookie]) }}",
        cookie_lang = "{{ app()->getLocale() }}";
</script>
--}}

{{--
    Javascript parts which should be included in the header.
--}}
@stack('scripts-head')
