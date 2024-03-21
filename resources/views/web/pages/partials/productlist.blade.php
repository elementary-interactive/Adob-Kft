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
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1" style="
																	border: none;
																	background-color: transparent;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                                        <path
                                            d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z">
                                        </path>
                                    </svg>
                                </span>
                                <input class="form-control" id="search-name" placeholder="szűrés..." type="text">
                            </div>
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
                            <a
                                href="{{ route('product.show', [
                                    'slug' => $product->slug,
                                ]) }}"><img
                                    height="100" alt=""
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
                        <td><a
                                href="{{ route('product.show', [
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
