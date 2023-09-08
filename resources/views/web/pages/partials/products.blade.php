@if ($products?->count())
    <div class="container-fluid fix-width">
        <div class="row">
            <div class="col-12">

                @include('web.pages.partials.productlist', [
                    'products'    => $products,
                    'search_term' => null
                ])

                @include('web.pages.partials.pagination', [
                    'pager' => $products,
                ])

            </div>
        </div>
    </div>
@endif
