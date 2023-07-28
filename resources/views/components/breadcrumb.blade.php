<ol class="breadcrumb">
    @if (isset($brand))
        <li><a href="{{ $basic_route = route('brands.browse', ['brand' => $brand->slug, 'slug' => '']) }}">{{ $brand->name }}</a></li>
    @else
        <li><a href="{{ $basic_route = route('product.browse', ['slug' => '']) }}">Start</a></li>
    @endif
    @foreach ($path as $path_item)
        @if ($loop->last)
            @if (isset($is_product) && $is_product == true)
                <li><a href="{{ $basic_route .= '/' . $category->slug }}">{{ $category->name }}</a></li>
            @elseif (isset($category))
                <li>{{ $category->name }}</li>
            @endif
        @else
            <li><a href="{{ $basic_route .= '/' . $path_item->slug }}">{{ $path_item->name }}</a></li>
        @endif
    @endforeach
</ol>
