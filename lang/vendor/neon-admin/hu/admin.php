<?php

return [
  "actions"    => [
    "acivate"       => [
      "label"         => "Kiválasztott elemek aktiválása"
    ],
    "inacivate"     => [
      "label"         => "Kiválasztott elemek inaktiválása"
    ],
  ],
  "navigation" => [
    "home"          => "Kezdőlap",
    "settings"      => "Beállítások",
    "web"           => "Weboldal",
    "site"          => "Domainek",
    "menu"          => "Menük",
    "news"          => "Hírek",
  ],
  "models" => [
    "admin"         => "Adminisztrátor",
    "admins"        => "Adminisztrátorok",
    "attribute"     => "Attribútum",
    "attributes"    => "Attribútumok",
    "site"          => "Oldal",
    "sites"         => "Oldalak",
    "menu"          => "Menü",
    "menus"         => "Menük",
    "news_item"     => "Hír",
    "news"          => "Hírek",
  ],
  "resources" => [
    "generic"       => [
      "form"          => [
        "tabs"          => [
          "basic"         => "Általános adatok",
          "attributables" => "Kiegészítő beállítások"
        ]
      ]
    ],
    "admins"        => "Adminisztrátorok",
    "attributables"  => [
      "title"         => "Változók",
      "form"          => [
        "fieldset"      => [
          "name"          => 'Név',
        ],
        "fields"        => [
          "class"         => [
            "label"         => "Erőforrás",
          ],
          "name"          => [
            "label"         => "Név"
          ],
          "slug"          => [
            "label"         => "Azonosító"
          ],
          "cast_as"       => [
            "label"         => "Kezelés, mint",
            "help"          => "Technikai paraméter, a változó ilyen erőforrásként lesz elmentve az adatbázisban.",
            "options"       => [
              "string"        => "Szöveg",
              "integer"       => "Egész szám",
              "float"         => "Lebegőpontos szám",
              "boolean"       => "Logikai (igaz/hamis)",
              "array"         => "Tömb",
            ]
          ],
          "field"         => [
            "label"         => "Beviteli mező",
            "options"       => [
              "text"         => "Szöveges beviteli mezők",
            ],
          ],
          "rules"         => [
            "label"         => "Szabályok",
            "options"       => [
              "activeUrl"     => "URL",
              "alpha"         => "Csak A-Z betűk",
              "alphaDash"     => "A-Z betűk és - _",
              "alphaNum"      => "Számok és betűk",
              "required"      => "Kötelező kitölteni",
              "ascii"         => "ASCII",
            ],
          ],
          "params"          => [
            "label"         => "Paraméterek"
          ],
          "slug"          => [
            "label"         => "Azonosító"
          ]
        ],
      ],
    ],
    "variables"     => "Változók",
    "sites"         => [
      "title"         => "Oldal",
      "table"         => [],
      "form"          => [
        "fields"        => [
          "locale"        => [
            "label"         => "Lokalizáció"
          ],
          "domains"       => [
            "label"         => "Domainek",
            "placeholder"   => "Domain hozzáadása http vagy https nélkül.",
            "help"          => "Minden, ez alá a domain alá bekötött elem csak erről a daomainről lesz elérhető.",
            "new"           => "Új domain hozzáadaása"
          ],
          "prefixes"       => [
            "label"         => "Csoportok",
            "placeholder"   => "Csoport hozzáadása.",
            "help"          => "Any kind of items under this site will be accessible only on these prefixes.",
            "new"           => "Új csoport hozzáadása."
          ],
          "is_default"    => [
            "label"         => "Alapértelmezett?"
          ],
          "title"         => [
            "label"         => "Név"
          ],
          "slug"          => [
            "label"         => "Azonosító"
          ]
        ],
        "fieldset"        => [
          "name"            => "Név"
        ]
      ]
    ],
    "menu"         => [
      "title"         => "Menu",
      "table"         => [],
      "form"          => [
        "fieldset"      => [
          "name"          => 'Név',
        ],
        "fields"        => [
          "title"         => [
            "label"         => "Név"
          ],
          "slug"          => [
            "label"         => "Azonosító"
          ],
          "site"          => [
            "label"         => "Weboldal"
          ],
          "status"          => [
            "label"         => "Státusz"
          ]
        ]
      ]
    ],
    "news"         => [
      "title"         => "Hírek",
      "table"         => [
        'tabs'          => [
          'all'           => 'Összes',
          'new'           => 'Új',
          'live'          => 'Publikus',
          'pinned'        => 'Kitűzött',
          'archive'       => 'Arhív',
        ]
      ],
      "form"          => [
        "filters"       => [
          "is_active"     => "Aktív?",
          "is_published"  => "Publikálva?"
        ],
        "fieldset"      => [
          "publishing"     => "Publikálási beállítások"
        ],
        "fields"        => [
          "title"         => [
            "label"         => "Cím"
          ],
          "slug"          => [
            "label"         => "Link"
          ],
          "header_image"  => [
            "label"         => "Fejléc kép"
          ],
          "lead"          => [
            "label"         => "Lead"
          ],
          "content"       => [
            "label"         => "Tartalom"
          ],
          "content_image" => [
            "label"         => "Képek"
          ],
          "site"          => [
            "label"         => "Weboldal"
          ],
          "status"        => [
            "label"         => "Státusz"
          ],
          "tags"          => [
            "label"         => "Címkék"
          ],
          "pinned"        => [
            "label"         => "Kitűzés"
          ],
          "published_at"  => [
            "label"         => "Publikálva"
          ],
          "expired_at"    => [
            "label"         => "Lejár"
          ]
        ]
      ]
    ]
  ]
];
