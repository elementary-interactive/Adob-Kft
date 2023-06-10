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

  <!-- brands -->

  <div class="container-fluid fix-width">
    <div class="row">
      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-brand_item" role="button">
          <img src="/images/brand/tungsram.png" alt="">
          <h3>TUNGSRAM</h3>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-brand_item" role="button">
          <img src="/images/brand/eta.png" alt="">
          <h3>ETA</h3>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-brand_item" role="button">
          <img src="/images/brand/gogen.webp" alt="">
          <h3>GOGEN</h3>
        </div>
      </div>

    </div>
  </div>

  <br><br><br>

  <!-- categories -->

  <div class="container-fluid">
    <div class="row">
      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="adob-cat_item" role="button">
          <h3>Autós</h3>
          <span>3 termék</span>
        </div>
      </div>

    </div>
  </div>

  <br><br><br>

  <!-- search -->

  <div class="container-fluid search-container">
    <div class="row">
      <div class="col-12">
        <div class="accordion" id="accordionExample">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                aria-expanded="true" aria-controls="collapseOne">
                38 termék találat
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <div class="container-fluid">
                  <div class="row">
                    <div class="col-12">
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
                              <tr role="button" tabindex="0">
                                <td>
                                  <img alt="" src="https://adob-prod-assets.fra1.digitaloceanspaces.com/74716/conversions/thumb.png">
                                </td>
                                <td>Grundig</td>
                                <td>XEH25215074</td>
                                <td>Halloween dekoráció pókok 3 db</td>
                              </tr>
                              <tr role="button" tabindex="0">
                                <td>
                                  <img alt="" src="https://adob-prod-assets.fra1.digitaloceanspaces.com/74716/conversions/thumb.png">
                                </td>
                                <td>Grundig</td>
                                <td>XEH25215074</td>
                                <td>Halloween dekoráció pókok 3 db</td>
                              </tr>
                              <tr role="button" tabindex="0">
                                <td>
                                  <img alt="" src="https://adob-prod-assets.fra1.digitaloceanspaces.com/74716/conversions/thumb.png">
                                </td>
                                <td>Grundig</td>
                                <td>XEH25215074</td>
                                <td>Halloween dekoráció pókok 3 db</td>
                              </tr>
                              <tr role="button" tabindex="0">
                                <td>
                                  <img alt="" src="https://adob-prod-assets.fra1.digitaloceanspaces.com/66012/conversions/thumb.png">
                                </td>
                                <td>Grundig</td>
                                <td>XEH25215074</td>
                                <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                                  labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris
                                  nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit
                                  esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
                                  culpa qui officia deserunt mollit anim id est laborum.</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                                  </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <br><br><br>

  <!-- product -->

  <div class="container-fluid product-container">
    <div class="row">
      <div class="col-12 col-xl-6">
        <section id="main-carousel" class="splide"
          aria-label="The carousel with thumbnails. Selecting a thumbnail will change the Beautiful Gallery carousel.">
          <div class="splide__track">
            <ul class="splide__list">
              <li class="splide__slide">
                <img src="/images/products/01.jpg" alt="">
              </li>
              <li class="splide__slide">
                <img src="/images/products/02.jpg" alt="">
              </li>
              <li class="splide__slide">
                <img src="/images/products/02.jpg" alt="">
              </li>
            </ul>
          </div>
        </section>
        <ul id="thumbnails" class="thumbnails">
          <li class="thumbnail">
            <img src="/images/products/01.jpg" alt="">
          </li>
          <li class="thumbnail">
            <img src="/images/products/02.jpg" alt="">
          </li>
          <li class="thumbnail">
            <img src="/images/products/02.jpg" alt="">
          </li>
        </ul>
      </div>
      <div class="col-12 col-xl-6 product-infos">
        <h2 class="blue">Füldugó TWS szabadkezes HEADSET BlueTooth hordozóval, fekete GRUNDIG</h2>
        <div class="row">
          <div class="col-md-12">
            <dl>
              <dt>Márka</dt>
              <dd>ALLRIDE</dd>
            </dl>
            <dl>
              <dt>Cikkszám</dt>
              <dd>XEH25222733</dd>
            </dl>
            <dl>
              <dt>EAN</dt>
              <dd>871125222733</dd>
            </dl>
            <dl>
              <dt>Ár</dt>
              <dd>3487</dd>
            </dl>
          </div>
        </div>
        <div class="caption">
          <p class="text-justify">Súly: 0,875Kg. 280x280x90mm. 12db/karton. EAN13: 8711252227337</p>
        </div>
      </div>
    </div>
  </div>

  <br><br><br>

  <!-- productlist -->

  <div class="container-fluid fix-width">
    <div class="row">
      <div class="col-12">
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
                <tr role="button" tabindex="0">
                  <td>
                    <img alt="" src="https://adob-prod-assets.fra1.digitaloceanspaces.com/74716/conversions/thumb.png">
                  </td>
                  <td>Grundig</td>
                  <td>XEH25215074</td>
                  <td>Halloween dekoráció pókok 3 db</td>
                </tr>
                <tr role="button" tabindex="0">
                  <td>
                    <img alt="" src="https://adob-prod-assets.fra1.digitaloceanspaces.com/74716/conversions/thumb.png">
                  </td>
                  <td>Grundig</td>
                  <td>XEH25215074</td>
                  <td>Halloween dekoráció pókok 3 db</td>
                </tr>
                <tr role="button" tabindex="0">
                  <td>
                    <img alt="" src="https://adob-prod-assets.fra1.digitaloceanspaces.com/74716/conversions/thumb.png">
                  </td>
                  <td>Grundig</td>
                  <td>XEH25215074</td>
                  <td>Halloween dekoráció pókok 3 db</td>
                </tr>
                <tr role="button" tabindex="0">
                  <td>
                    <img alt="" src="https://adob-prod-assets.fra1.digitaloceanspaces.com/66012/conversions/thumb.png">
                  </td>
                  <td>Grundig</td>
                  <td>XEH25215074</td>
                  <td>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                    labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris
                    nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit
                    esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
                    culpa qui officia deserunt mollit anim id est laborum.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="pagination">
          <a href="#" class="prev"><i class="icon-left-open"></i></a>
          <a href="#">1</a>
          <a href="#" class="active">2</a>
          <a href="#">3</a>
          <a href="#" class="next"><i class="icon-right-open"></i></a>
      </div>
      </div>
    </div>
  </div>

  <br><br><br>

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

  <br><br><br>

  <!-- about -->

  <div class="container-fluid fix-width pricelists-container">
    <div class="row">
      <div class="col-12">
        <h1>Rólunk</h1>
        <p>Az Adob Kft. csak nagykereskedelmi tevékenységet végez, magánszemélyeket nem szolgálunk ki.</p>

        <ul>
          <li>Weben keresztül nem történik értékesítés.</li>
          <li>A feltüntetett árak csak jelzés értékűek, tényleges árakat személyes ajánlatban küldünk.</li>
          <li>A cikkszámhoz tartozó web-cím szövege az adott termék jellegére vonatkozóan tartalommal nem bír.</li>
          <li>A képek és termékinformációk csak tájékoztató jellegűek. Az esetleges eltérésekért és hibákért az Adob
            Kft. nem vállal felelősséget. </li>
        </ul>

        <h2>Cookie nyilatkozat</h2>
        <p>Cookie-kat nem tárolunk.</p>

        <h2>Kapcsolat</h2>
        <p>email: kapcsolat [kukac] adob.hu</p>
        <p>mobil: +36 30 110 6126</p>
      </div>
    </div>
  </div>

@endsection
