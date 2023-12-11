<div class="product-list-container inputs">
    <div class="product-list-box">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>kép</th>
                    <th>
                        @if ($products->unique('brand')->count() > 1)
                            <select class="form-control" id="search-brand">
                                <option value="______">összes</option>
                                @foreach ($products->unique('brand') as $product)
                                    <option value="{{ $product->brand?->slug }}">{{ $product->brand?->name }}</option>
                                @endforeach
                            </select>
                        @else
                            márka
                        @endif
                    </th>
                    <th>cikkszám</th>
                    <th>
                        <span st-sort="name" role="columnheader" aria-sort="none">megnevezés</span>
                        <span class="product-list_common-search-bar">
                            <input class="form-control" id="search-name" placeholder="keresés..." type="text">
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody id="search-data">
                @foreach ($products as $product)
                    <tr role="button" data-brand="{{ $product->brand?->slug }}"
                        data-href="{{ route('product.show', [
                            'slug' => $product->slug,
                        ]) }}"
                        tabindex="0">
                        <td>
                            <a href="{{ route('product.show', [
                                'slug' => $product->slug,
                            ]) }}"><img
                                    height="100"
                                    alt=""
                                    src="{{ $product->getFirstMediaUrl(\App\Models\Product::MEDIA_COLLECTION, 'thumb') }}"></a>
                        </td>
                        <td><a
                                href="{{ route('product.show', [
                                    'slug' => $product->slug,
                                ]) }}">{{ $product->brand?->name }}</a>
                        </td>
                        <td><a
                                href="{{ route('product.show', [
                                    'slug' => $product->slug,
                                ]) }}">{{ $product->product_id }}</a>
                        </td>
                        <td><a href="{{ route('product.show', [
                            'slug' => $product->slug,
                        ]) }}">
                                @if ($search_term)
                                    {!! Str::swap([$search_term => '<span class="highlighted">' . $search_term . '</span>'], $product->name) !!}
                                    ˇ
                                @else
                                    {!! $product->name !!}
                                @endif
                            </a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script type="module">
    $(document).ready(function($) {
        $("#search-brand").change(function() {
            var b = $("#search-brand").val();

            $("#search-data").children('tr').each(function() {
                if (b == '______' || $(this).data('brand') == b) {
                    $(this).show();
                } else if ($(this).data('brand') != b) {
                    $(this).hide();
                }
            });
        });
        $("#search-name").keypress(function() {
            var q = $("#search-name").val().toLowerCase();

            $("#search-data").children('tr').each(function() {
                if (q == '') {
                    $(this).show();
                } else {
                    var f = false;
                    $(this).children('td').each(function() {
                        var x = $(this).children('a:first').html().toLowerCase()
                            .indexOf(q);

                        if (x > -1) {
                            f = true;
                        }
                    })
                    if (f) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                }
            });
        });
    });
</script>
