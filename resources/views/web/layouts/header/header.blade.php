{{-- Top bar. --}}

{{-- <x-neon-menu id="fomenu"> <x-slot:tools> ... </x-slot> </x-neon-menu> --}}

<header>

  <!-- nav -->

  <nav class="navbar navbar-expand-xl white-bg">
    <a class="navbar-brand" href="/"><img src="{{ Vite::asset('/resources/images/adob_medium.png') }}" alt=""></a>

    <div class="mobile-menu">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-menu"
        aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="icon-bar top-bar"></span>
        <span class="icon-bar middle-bar"></span>
        <span class="icon-bar bottom-bar"></span>
      </button>
    </div>
    <div class="collapse navbar-collapse" id="main-menu">
      <ul class="navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="/">Termékek</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/brands">Márkák</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/pricelists">Letölthető árlisták</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/about">Rólunk</a>
        </li>
      </ul>

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

  </nav>

  <ol class="breadcrumb">
    <li><a href="">Start</a></li>
    <li><a href="">Audio</a></li>
    <li>Gyerekeknek</li>
  </ol>

</header>