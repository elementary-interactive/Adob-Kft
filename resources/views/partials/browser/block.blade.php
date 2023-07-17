<div class="container-fluid">
    @inject ('category_service', \App\Services\CategoryService::class)

    @php
        $category = $category_service->items(request('slug'));
    @endphp

    <div class="row">
        @forelse ($category as $item)
            {{ dd($item->getAncestorsAndSelfWithoutRoot()) }}
            @if ($item->counts)
                @include ('partials.browser.item', [
                    'item' => $item
                ])
            @endif
        @empty
            
        @endforelse
    </div>
</div>
