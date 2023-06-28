@props([
  'searchbox'
])
<nav class="navbar navbar-expand-xl white-bg">
    <a class="navbar-brand" href="/"><img src="/images/adob_medium.png" alt=""></a>

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
        @foreach ($links as $link)
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="{{ $link->href }}">{{ $link->title }}</a>
        </li>
        @endforeach
      </ul>

     {{ $searchbox }}

  </nav>