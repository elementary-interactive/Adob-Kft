<ol class="breadcrumb">
  <li><a href="{{ $slug }}">Start</a></li>
    @foreach ($path as $path_item)
    <li><a href="{{ $slug .= '/' . $path_item->slug }}">{{ $path_item->name }}</a></li>
    @endforeach
    @if ($is_product)
    <li><a href="{{ $slug .= '/' . $current->slug }}">{{ $current->name }}</a></li>
    @else
    <li>{{ $current->name }}</li>
    @endif
  </ol>