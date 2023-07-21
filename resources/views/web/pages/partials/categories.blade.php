@if ($categories?->count())
    <div class="container-fluid">
        <div class="row">
            @foreach ($categories as $category_item)
                @if ($category_item->counts)
                    @include ('partials.browser.item', [
                        'brand' => $brand,
                        'item'  => $category_item,
                    ])
                @endif
            @endforeach
        </div>
    </div>
@endif
