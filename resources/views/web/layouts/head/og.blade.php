@isset($og)
  @foreach($og as $property => $value)
<meta property="og:{{ $property }}" content="{{ $value }}" />
  @endforeach
<meta property="og:locale" content="{{ app()->getLocale() }}" />
@endisset
