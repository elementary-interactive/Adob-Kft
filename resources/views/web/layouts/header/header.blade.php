<header>
  <!-- nav -->
  <x-neon-menu id="fomenu">
    <x-slot:searchbox>
      <div class="search-box 
      @if (request()->has('query'))
      active
      @endif ">
        <form method="GET" action="{{ route('search') }}">
        <input type="text" name="query" placeholder="Keresés..." value="{{ request()->get('query') }}" class="
        @if (request()->has('query'))
        active
        @endif">
        <div class="search-icon
        @if (request()->has('query'))
        active
        @endif">
          <i class="icon-search"></i>
        </div>
        <div class="cancel-icon
        @if (request()->has('query'))
        active
        @endif">
          <i class="icon-cancel"></i>
        </div>
        <button class="btn defbtn" type="submit">Keresés</button>
        </form>
      </div>
    </x-slot>
  </x-neon-menu>
  @stack('breadcrumb')
</header>