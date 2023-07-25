<div class="product-list-container inputs">
  <div class="product-list-box">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>kép</th>
          <th>
            <select class="form-control" st-search="brand">
              <option value="">összes</option>
              <option value="Grundig">Grundig</option>
              <option value="Nobrand">Nobrand</option>
            </select>
          </th>
          <th>cikkszám</th>
          <th>
            <span st-sort="name" role="columnheader" aria-sort="none">megnevezés</span>
            <span class="product-list_common-search-bar">
              <input class="form-control" st-search="" placeholder="keresés..." type="text">
            </span>
          </th>
        </tr>
      </thead>
      <tbody>
        @foreach ($products as $product)
        
        <tr role="button" data-href="{{ route('product.show', [
          'slug'  => $product->slug
        ])}}" tabindex="0">
          <td>
            <a href="{{ route('product.show', [
          'slug'  => $product->slug
        ])}}"><img alt="" src="{{ $product->getFirstMediaUrl(\App\Models\Product::MEDIA_MAIN, 'thumb') }}"></a>
          </td>
          <td><a href="{{ route('product.show', [
            'slug'  => $product->slug
          ])}}">{{ $product->brand->name }}</a></td>
          <td><a href="{{ route('product.show', [
            'slug'  => $product->slug
          ])}}">{{ $product->product_id }}</a></td>
          <td><a href="{{ route('product.show', [
            'slug'  => $product->slug
          ])}}">{!! $product->name !!}</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
{{-- <script type="text/javascript">
  jQuery(document).ready(function($) {
      $(".clickable").click(function() {
          window.location = $(this).data("href");
      });
      $(".clickable").contextmenu(function() {
        window.location = $(this).data("href");
    });
  });
</script> --}}