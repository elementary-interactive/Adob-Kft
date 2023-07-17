@isset($meta)
  @foreach($meta as $property => $value)
<meta name="{{ $property }}" content="{{ $value }}" />
  @endforeach
<meta name="engine" content="N23" />
@endisset
