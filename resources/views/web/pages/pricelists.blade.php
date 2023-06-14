@extends('web.layouts.default')

@section('title', __('meta.impressum.title'))

@push('og')
  @include('web.layouts.head.og', ['og' => [
      'title'             => __('meta.impressum.title'),
      'description'       =>  __('meta.impressum.description'),
      'type'              => 'info-page',
      'url'               => \Request::url()
  ]])
@endpush


@section('body')

  <!-- pricelists -->

  <div class="container-fluid fix-width pricelists-container">
    <div class="row">
      <div class="col-12">
        <h1>Letölthető árlisták</h1>
        <p>Az ártlista megtekintéshez kattints a kék címre.</p>
        <p><br></p>
        <p><a
            href="https://adobkft-my.sharepoint.com/:x:/g/personal/attiladob_adobkft_onmicrosoft_com/ETgUbsMBLl9Gt7R_TtrgMI8BS3-k9uDYQN8TXzezxhOyxw?e=FdCGmp&amp;wdLOR=c2B4C9FEF-6ECF-45E7-990F-B65073E6DE3A"
            target="_blank" class="ql-size-large"><strong>COVID</strong></a></p>
        <ul>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_teszt!A1" target="_blank"
              class="ql-size-small"><strong>Teszt
              </strong></a><span class="ql-size-small">→</span> <span class="ql-size-small">Antitest szintmérő,
              Antigén, Antitest,
              Hőmérő. Nyalókás, nyálas, fájdalommentes tesztek is.</span></li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_MASZK!A1" target="_blank"
              class="ql-size-small"><strong>Maszk
              </strong></a><span class="ql-size-small">→ </span><span class="ql-size-small">AKCIÓS termékek, maszk,
              lázmérő, pulzoxy.</span></li>
          <li><a
              href="https://mail.google.com/mail/u/0/#m_5039842065837610066_keszty%C5%B1!A1"><strong>Kesztyű</strong></a><span
              class="ql-size-small">→ </span><span class="ql-size-small">Latex, nitril, vinyl, steril,
              takarító kesztyű</span></li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_KIEG%C3%89SZ%C3%8DT%C5%90K!A1"><strong>Kiegészítők
              </strong></a><span class="ql-size-small">→ </span><span class="ql-size-small">Pajzs, szemüveg, oldódó
              zsák,
              cipővédő, hajháló, lepedő, alkarvédő.</span></li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_Over%C3%A1l!A1"><strong>Overál
              </strong></a><span class="ql-size-small">→ </span><span class="ql-size-small">Cat 3, PP, PE
              overál.</span></li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_k%C3%B6peny!A1"><strong>Köpeny
              </strong></a><span class="ql-size-small">→ </span><span class="ql-size-small">Látogató és orvosi köpeny.
              Steril
              köntös és köpeny.</span></li>
          <li><a
              href="https://mail.google.com/mail/u/0/#m_5039842065837610066_'sz%C3%BCl%C5%91-szett'!A1"><strong>Szülő-szett</strong></a><span
              class="ql-size-small">→ </span><span class="ql-size-small">Szülő-látogató komplett
              szett</span></li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_zsilip!A1" target="_blank"
              class="ql-size-small"><strong>Zsilipruha
              </strong></a><span class="ql-size-small">→ </span><span class="ql-size-small">PP, SS, PPE
              zsilipruha.</span></li>
          <li><a
              href="https://mail.google.com/mail/u/0/#m_5039842065837610066_fert%C5%91tlen%C3%ADt%C5%91!A1"><strong>Fertőtlenítő
                folyadék</strong></a><span class="ql-size-small">→
            </span><span class="ql-size-small">Kéz és felület. Gél és folyadék. Zoono tartós kéz és felület
              fertőtlenítő.</span></li>
        </ul>
        <p><a
            href="https://adobkft-my.sharepoint.com/:x:/g/personal/attiladob_adobkft_onmicrosoft_com/ETjgor_d0tJHgls-Zvz-M2sBT_tpcZx3HmHdk8VcbrdP6A?wdLOR=cEDE6198B-3A12-4D98-B62A-F421504E28DC"
            target="_blank" class="ql-size-large"><strong>Patika-drogéria</strong></a></p>
        <ul>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_p%C3%A1r%C3%A1s%C3%ADt%C3%B3!A1"
              target="_blank"><strong>Párásító</strong></a>
            <span>→
            </span>Aroma és ultrahangos párásító, légkezelés.
          </li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_kend%C5%91!A1"
              target="_blank"><strong>Törlőkendő
              </strong></a><span>→ </span>Antibakteriális, baba, ápolás, folyékony
            szappan, vegyszermentes kendő (WaterWipes).</li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_'patika-drog%C3%A9ria'!A1"
              target="_blank"><strong>Patika, drogéria
              </strong></a><span>→ </span>Sebtapasz, kinezio, ízület szorító,
            kullancs, adagoló, kozmetika, pedikűr-manikűr, borotva, fésű, reszelő, talpbetét.</li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_'fog-haj'!A1"
              target="_blank"><strong>Fog, haj - ápolás
              </strong></a><span>→ </span>ETA ultrahangos fogkefe, hajvágó, borotva,
            hajszárító, sarokreszelő.</li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_m%C3%A9rleg!A1"
              target="_blank"><strong>Mérleg
              </strong></a><span>→ </span>Eta, Emos konyhai és személyi mérleg.</li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_pipere!A1"
              target="_blank"><strong>Piperetáska</strong></a> <span>→ </span>Kozmetikai táska.</li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_Fitness!A1"
              target="_blank"><strong>Fitness </strong></a><span>→ </span>Umbro, Dunlop fitness termékek.</li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_RANGE!A1"
              target="_blank"><strong>Ledkínáló </strong></a><span>→ </span>Ledes kulcstartó gyerekeknek, praktikus
            kínálóban. Autó,
            állat figurák, világegyetem.</li>
          <li><a href="https://mail.google.com/mail/u/0/#m_5039842065837610066_sz%C3%BAnyog!A1"
              target="_blank"><strong>Szúnyog
              </strong></a><span>→ </span>Szúnyog és kullancsriasztó karkötő
            kínálóban.</li>
        </ul>
      </div>
    </div>
  </div>

@endsection
