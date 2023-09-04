<header>
  <!-- nav -->
  <x-neon-menu id="fomenu">
    <x-slot:searchbox>
      <div class="search-box">
        <form method="GET" action="{{ route('search') }}">
        <input type="text" name="query" placeholder="Keresés...">
        <div class="search-icon">
          <i class="icon-search"></i>
        </div>
        <div class="cancel-icon">
          <i class="icon-cancel"></i>
        </div>
        <button class="btn defbtn" type="submit">Keresés</button>
        </form>
      </div>
    </x-slot>
  </x-neon-menu>
  @stack('breadcrumb')
</header>