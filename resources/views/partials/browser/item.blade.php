<div class="col-6 col-sm-4 col-md-3 col-lg-2">
    <a href="{{ route($route, [
        'brand' => $brand?->slug,
        'slug'  => $item->fullSlug
    ]) }}" class="adob-cat_item">
        <h3>{{ $item->name }}</h3>
        <span>{{ $item->counts }} termék</span>
    </a>
</div>