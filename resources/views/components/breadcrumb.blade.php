<ol class="breadcrumb">
  <li><a href="{{ $slug }}">Start</a></li>
    @foreach ($path as $path_item)
    <li><a href="{{ $slug .= '/' . $path_item->slug }}">{{ $path_item->name }}</a></li>
    @endforeach
    <li>{{ $current->name }}</li>
  </ol>