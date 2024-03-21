<div class="pagination">
    @if ($pager->total() > $pager->perPage())
        <a href="{{ $pager->previousPageUrl() }}" class="prev"><i class="icon-left-open"></i></a>
    @endif
    {{-- @dump($pager->currentPage(), $pager->lastPage()) --}}
    @if ($pager->lastPage() > 5)
        @if ($pager->currentPage() >= 5)
            <a href="{{ $pager->url(1) }}">1</a>
            @if ($pager->currentPage() > 15)
                @for ($p = 1; $p < ceil(($pager->currentPage() - 2) / 10); $p++)
                    <a href="{{ $pager->url($p * 10) }}">{{ $p * 10 }}</a>
                @endfor
            @endif
            <span style="line-height: 4rem;">
                ...
            </span>
            @if ($pager->currentPage() < $pager->lastPage() - 3)
                @for ($p = $pager->currentPage() - 2; $p <= $pager->currentPage() + 2; $p++)
                    <a href="{{ $pager->url($p) }}"
                        @if ($p == $pager->currentPage()) class="active" @endif>{{ $p }}</a>
                @endfor
                <span style="line-height: 4rem;">
                    ...
                </span>
                @for ($p = ceil(($pager->currentPage() + 3) / 10); $p <= $pager->lastPage() / 10; $p++)
                    <a href="{{ $pager->url($p * 10) }}">{{ $p * 10 }}</a>
                @endfor
            @else
                @for ($p = $pager->lastPage() - 5; $p <= $pager->lastPage(); $p++)
                    <a href="{{ $pager->url($p) }}"
                        @if ($p == $pager->currentPage()) class="active" @endif>{{ $p }}</a>
                @endfor
            @endif
        @else
            @for ($p = 1; $p <= 5; $p++)
                <a href="{{ $pager->url($p) }}"
                    @if ($p == $pager->currentPage()) class="active" @endif>{{ $p }}</a>
            @endfor
            <span style="line-height: 4rem;">
                ...
            </span>
            @for ($p = ceil(($pager->currentPage() + 3) / 10); $p <= $pager->lastPage() / 10; $p++)
                <a href="{{ $pager->url($p * 10) }}">{{ $p * 10 }}</a>
            @endfor
        @endif

        {{-- @for ($p = 1; $p <= $pager->lastPage(); $p++)
    <a href="{{ $pager->url($p) }}"
      @if ($p == $pager->currentPage())
      class="active"
      @endif
    >{{ $p }}</a>
    @endfor --}}
    @else
        @for ($p = 1; $p <= $pager->lastPage(); $p++)
            <a href="{{ $pager->url($p) }}"
                @if ($p == $pager->currentPage()) class="active" @endif>{{ $p }}</a>
        @endfor
    @endif
    @if ($pager->total() > $pager->perPage())
        <a href="{{ $pager->nextPageUrl() }}" class="next"><i class="icon-right-open"></i></a>
    @endif
    <form method="GET" action="{{ url()->current() }}">
      <input type="hidden" name="query" value="{{ request()->get('query') }}">
        <div class="row" style="line-height: 4rem;">
        <div class="col-auto" style="margin-left: 4rem;">
                <label class="d-inline-block">Megjelenített termékek: </label>
        </div>
        <div class="col-auto" style="max-width: 16rem !important; min-height: 4rem !important;">
                <select class="form-control d-inline-block" id="perPage" name="perPage"
                    onchange="this.form.submit();">
                    <option value="25" @if (request()->get('perPage', 25) == 25) selected @endif>25</option>
                    <option value="50" @if (request()->get('perPage', 25) == 50) selected @endif>50</option>
                    <option value="100" @if (request()->get('perPage', 25) == 100) selected @endif>100</option>
                </select>
            </div>
        </div>
    </form>
</div>
