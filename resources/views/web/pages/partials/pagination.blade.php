<div class="pagination">
  @if ($pager->total() > $pager->perPage())
  <a href="{{ $pager->previousPageUrl() }}" class="prev"><i class="icon-left-open"></i></a>
  @endif
  @for ($p = 1; $p <= $pager->lastPage(); $p++ )
  <a href="{{ $pager->url($p) }}"
    @if ($p == $pager->currentPage())
    class="active"
    @endif
  >{{ $p }}</a>
  @endfor
  @if ($pager->total() > $pager->perPage())
  <a href="{{ $pager->nextPageUrl() }}" class="next"><i class="icon-right-open"></i></a>
  @endif
  </div>