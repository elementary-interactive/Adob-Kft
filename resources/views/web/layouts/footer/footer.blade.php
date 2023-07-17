{{--
    Footer part.
--}}
<footer>
  <div class="footer-wrapper">
    <h4>{{ app('site')->current()->lablec_cegnev }}</h4>
    <ul class="general">
      <li class="copy me-2">{!! app('site')->current()->lablec_copyright !!}</li>
      <li>{!! app('site')->current()->lablec_tax !!}</li>
    </ul>
    <ul class="contact-info">
      @if (app('site')->current()->lablec_email)
      <li>email: <a href="mailto:{{ app('site')->current()->lablec_email }}">{{ app('site')->current()->lablec_email }}</a></li>
      @endif
      @if (app('site')->current()->lablec_phone)
      <li>mobil: <a href="tel:{{ app('site')->current()->lablec_phone }}">{{ app('site')->current()->lablec_phone }}</a></li>
      @endif
    </ul>
    <div class="info">{!! app('site')->current()->lablec_disclaimer !!}</div>
  </div>
</footer>