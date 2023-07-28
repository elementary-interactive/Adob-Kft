<header>
  <!-- nav -->
  <x-neon-menu id="fomenu">
    <x-slot:searchbox>
      <div class="search-box">
        <input type="text" placeholder="Keresés...">
        <div class="search-icon">
          <i class="icon-search"></i>
        </div>
        <div class="cancel-icon">
          <i class="icon-cancel"></i>
        </div>
        <button class="btn defbtn" type="submit">Keresés</button>
      </div>
    </x-slot>
  </x-neon-menu>
  @stack('breadcrumb')
</header>