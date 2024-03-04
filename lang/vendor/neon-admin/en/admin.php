<?php

return [
  "actions"    => [
    "acivate"   => [
      "label"     => "Activate selected items"
    ],
    "inacivate" => [
      "label"     => "Inactivate selected items"
    ],
  ],
  "navigation" => [
    "home"       => "Home",
    "settings"   => "Settings",
    "web"        => "Website",
    "site"       => "Domains",
    "menu"       => "Menus",
    "menu_item"  => "Menu Items",
    "news"       => "News",
    "content"    => "Content",
    "slideshow"  => "Slideshow",
    "faq"        => "FAQs",
    "faq_category"
    => "FAQ Categories",
    "document"   => "Documents",
    "docuemnt_category"
    => "Documents Categories",
  ],
  "models"     => [
    "admin"      => "Administrator",
    "admins"     => "Administartors",
    "attribute"  => "Attribute",
    "attributes" => "Attributes",
    "site"       => "Site",
    "sites"      => "Sites",
    "menu"       => "Menu",
    "menus"      => "Menus",
    "menu_item"  => "Menu Item",
    "menu_items" => "Menu Items",
    "news_item"  => "News Post",
    "news"       => "News",
    "content"    => "Content",
    "contents"   => "Contents",
    "slideshow_item"
    => "Slideshow",
    "slideshow"  => "Slideshows",
    "faq"        => "FAQ",
    "faqs"       => "FAQs",
    "faq_category"
    => "FAQ Category",
    "faq_categories"
    => "FAQ Categories",
    "document"   => "Document",
    "documents"  => "Documents",
    "document_category"
    => "Document Category",
    "document_categories"
    => "Document Categories",
  ],
  "resources" => [
    "generic"       => [
      "form"          => [
        "tabs"          => [
          "basic"         => "Base settings",
          "attributables" => "Advanced settings"
        ]
      ]
    ],
    "admins"        => "Administrators",
    "attributables" => [
      "title"         => "Variables",
      "form"          => [
        "fieldset"      => [
          "name"          => 'Name',
        ],
        "fields"        => [
          "class"         => [
            "label"         => "Resource",
          ],
          "name"          => [
            "label"         => "Name"
          ],
          "slug"          => [
            "label"         => "Identifier"
          ],
          "cast_as"       => [
            "label"         => "Cast as",
            "help"          => "Technical parameter, how to store the variable in database.",
            "options"       => [
              "string"        => "String",
              "integer"       => "Integer",
              "float"         => "Float",
              "boolean"       => "Boolean (true/false)",
              "array"         => "Array",
            ]
          ],
          "field"         => [
            "label"         => "Input field",
            "options"       => [
              "text"         => "Text input",
            ],
          ],
          "rules"         => [
            "label"         => "Rules",
            "options"       => [
              "activeUrl"     => "URL",
              "alpha"         => "Only A-Z letters",
              "alphaDash"     => "A-Z letters and - _",
              "alphaNum"      => "Numbers and letters",
              "required"      => "Required",
              "ascii"         => "ASCII",
            ],
          ],
          "params"          => [
            "label"         => "Parameters"
          ],
          "slug"          => [
            "label"         => "Identifier"
          ]
        ],
      ],
    ],
    "variables"     => "Variables",
    "sites"         => [
      "title"         => "Sites",
      "table"         => [],
      "form"          => [
        "fields"        => [
          "locale"        => [
            "label"         => "Localization"
          ],
          "domains"       => [
            "label"         => "Domains",
            "placeholder"   => "Add a domain without http or https.",
            "help"          => "Any kind of items under this site will be accessible only on these domains.",
            "new"           => "Add a new domain"
          ],
          "prefixes"       => [
            "label"         => "Prefixes",
            "placeholder"   => "Add a prefix.",
            "help"          => "Any kind of items under this site will be accessible only on these prefixes.",
            "new"           => "Set a new prefix"
          ],
          "is_default"    => [
            "label"         => "Is default?"
          ],
          "title"         => [
            "label"         => "Name"
          ],
          "slug"          => [
            "label"         => "Identifier"
          ]
        ],
        "fieldset"        => [
          "name"            => "Name"
        ]
      ]
    ],
    "menu"          => [
      "title"         => "Menu",
      "table"         => [],
      "actions"       => [
        "items"         => 'Items...'
      ],
      "form"          => [
        "fieldset"      => [
          "name"          => 'Name',
        ],
        "fields"        => [
          "title"         => [
            "label"         => "Name"
          ],
          "slug"          => [
            "label"         => "Identifier"
          ],
          "site"          => [
            "label"         => "Website"
          ],
          "status"          => [
            "label"         => "Status"
          ],
          "items"         => [
            "label"         => "Items"
          ]
        ]
      ]
    ],
    "menu-item"         => [
      "title"         => "Menu item",
      "table"         => [],
      "actions"       => [
        "items"         => 'Items...'
      ],
      "form"          => [
        "fieldset"      => [
          "name"          => 'Name',
        ],
        "fields"          => [
          "menu"          => [
            "label"         => "Menu"
          ],
          "children"      => [
            "label"         => "Subment items",
            "add"           => "Add sub menu item",
          ],
          "link"          => [
            "label"         => "Content"
          ],
          "target"        => [
            "label"         => "Open to...",
            "options"       => [
              "self"          => "Self",
              "blank"         => "New Window",
            ]
          ],
          "is_outside"    => [
            "label"         => "Is Outside?",
            "help"          => "Select this, if you would to point this menu item to outside of the site, or just don't want to connect any content."
          ],
          "title"         => [
            "label"         => "Name"
          ],
          "slug"          => [
            "label"         => "URL",
            "help"          => "If \"Is Outside\" selected, you can put here a full URL starts with https:// to point to another site."
          ],
          "site"          => [
            "label"         => "Website"
          ],
          "status"        => [
            "label"         => "Status"
          ]
        ]
      ]
    ],
    "news"          => [
      "title"         => "News",
      "table"         => [
        'tabs'          => [
          'all'           => 'All',
          'new'           => 'New',
          'live'          => 'Live',
          'pinned'        => 'Pinned',
          'archive'       => 'Archive',
        ]
      ],
      "blocks"        => [
        "news-block"    => [
          "label"         => 'Recent News',
          "title"         => [
            "label"         => "Title",
          ],
          "subtitle"      => [
            "label"         => "Subtitle",
          ],
          "limit"         => [
            "label"         => "Limit",
            "help"          => "Maximum number of shown items.",
          ],
          "tags"          => [
            "label"         => "Filter tags",
            "help"          => "Show items only having these tags."
          ],
        ]
      ],
      "form"          => [
        "filters"       => [
          "is_active"     => "Is Active?",
          "is_published"  => "Is Published?"
        ],
        "fieldset"      => [
          "publishing"     => "Publishing"
        ],
        "fields"        => [
          "title"         => [
            "label"         => "Title"
          ],
          "slug"          => [
            "label"         => "Link"
          ],
          "header_image"  => [
            "label"         => "Header Image"
          ],
          "lead"          => [
            "label"         => "Lead"
          ],
          "content"       => [
            "label"         => "Content"
          ],
          "content_image" => [
            "label"         => "Content Images"
          ],
          "site"          => [
            "label"         => "Website"
          ],
          "tags"          => [
            "label"         => "Tags"
          ],
          "pinned"        => [
            "label"         => "Pinned"
          ],
          "status"        => [
            "label"         => "Status"
          ],
          "published_at"  => [
            "label"         => "Published at"
          ],
          "expired_at"    => [
            "label"         => "Expired at"
          ]
        ]
      ]
    ],
    "content"       => [
      "title"         => "Content",
      "table"         => [
        'tabs'          => [
          'all'           => 'All',
          'new'           => 'New',
          'live'          => 'Live',
          'pinned'        => 'Pinned',
          'archive'       => 'Archive',
        ]
      ],
      "form"          => [
        "tabs"          => [
          "content"       => 'Content'
        ],
        "filters"       => [
          "is_active"     => "Is Active?",
          "is_published"  => "Is Published?"
        ],
        "fieldset"      => [
          "name"          => "Naming",
          "publishing"    => "Publishing",
          "og_data"       => "Sharing"
        ],
        "fields"        => [
          "title"         => [
            "label"         => "Title"
          ],
          "slug"          => [
            "label"         => "Link",
            "help"          => "Via this link will be the page available under your site."
          ],
          "header_image"  => [
            "label"         => "Header Image"
          ],
          "lead"          => [
            "label"         => "Lead"
          ],
          "is_index"      => [
            "label"         => "Is index",
            "help"          => "Mark this page as the index page related on attached domains."
          ],
          "content"       => [
            "label"         => "Content",
            "new"           => "Add new block",
            "heading"       => [
              "label"         => "Header",
              "options"       => [
                "h1"            => "Header Level 1",
                "h2"            => "Header Level 2",
                "h3"            => "Header Level 3",
                "h4"            => "Header Level 4",
                "h5"            => "Header Level 5",
                "h6"            => "Header Level 6",
              ]
            ]
          ],
          "content_image" => [
            "label"         => "Content Images"
          ],
          "og_title"      => [
            "label"         => "Title"
          ],
          "og_image"      => [
            "label"         => "Image"
          ],
          "og_description" => [
            "label"         => "Description"
          ],
          "site"          => [
            "label"         => "Website"
          ],
          "tags"          => [
            "label"         => "Tags"
          ],
          "pinned"        => [
            "label"         => "Pinned"
          ],
          "status"        => [
            "label"         => "Status"
          ],
          "published_at"  => [
            "label"         => "Published at"
          ],
          "expired_at"    => [
            "label"         => "Expired at"
          ]
        ]
      ]
    ],
    "slideshow"     => [
      "title"         => "Slideshow",
      "table"         => [
        'tabs'          => [
          'all'           => 'All',
          'new'           => 'New',
          'live'          => 'Live',
          'archive'       => 'Archive',
        ]
      ],
      "form"          => [
        "fieldset"      => [
          "publishing"    => "Publishing",
          "items"         => "Items",
          "add_items"     => "Add Slide"
        ],
        "fields"        => [
          "title"         => [
            "label"         => "Title",
          ],
          "site"          => [
            "label"         => "Site",
          ],
          "items"         => [
            "label"         => "Items"
          ],
          "items_media"   => [
            "label"         => "Item's media"
          ],
          "status"        => [
            "label"         => "Status",
          ],
          "published_at"  => [
            "label"         => "Published At",
          ],
          "expired_at"    => [
            "label"         => "Expired At",
          ]
        ]
      ]
    ],
    "slideshow_items"
    => [
      "form"          => [
        "fieldset"      => [
          "items"         => [
            "label"         => "Slides",
            "button"        => "Add Slide"
          ]
        ],
        "fields"        => [
          "title"         => [
            "label"         => "Title",
            "help"          => "Headline on the slide. Leave blank if you just want to sohw the image.",
          ],
          "lead"          => [
            "label"         => "Lead"
          ],
          "media"          => [
            "label"         => "Image"
          ],
          "cta_text"      => [
            "label"         => "Text",
            "help"          => "Text of the CTA to show. It will be shown only, if you fill out the link.",
          ],
          "cta_link"         => [
            "label"         => "Link",
            "help"          => "If you want to show the button, and add text this link will be applied to that.",
          ],
          "status"          => [
            "label"         => "Status"
          ],
        ]
      ]
    ],
    "faq"           => [
      "form"          => [
        "fields"        => [
          "category"      => [
            "label"         => "FAQ Category",
          ],
          "question"      => [
            "label"         => "Question",
          ],
          "answer"        => [
            "label"         => "Answer",
          ],
          "media"         => [
            "label"         => "Media",
          ],
          "status"        => [
            "label"         => "Status",
          ],
          "published_at"  => [
            "label"         => "Published",
          ],
          "expired_at"    => [
            "label"         => "Expiring",
          ],
        ],
        "schema"        => [
          "publishing"    => [
            "label"         => "Publishing information"
          ]
        ],
      ],
    ],
    "faq_category"  => [
      "form"          => [
        "fields"        => [
          "site"          => [
            "label"         => "Site",
          ],
          "title"         => [
            "label"         => "Title",
          ],
          "slug"          => [
            "label"         => "Slug",
          ],
          "media"         => [
            "label"         => "Media",
          ],
          "lead"          => [
            "label"         => "Lead",
          ],
          "status"        => [
            "label"       => "Status",
          ],
          "published_at"  => [
            "label"       => "Published",
          ],
          "expired_at"    => [
            "label"       => "Expiring",
          ],
        ],
        "schema"      => [
          "publishing"  => [
            "label"       => "Publishing information"
          ]
        ]
      ],
    ],
    "document"      => [
      "form"          => [
        "fields"        => [
          "category"      => [
            "label"         => "Category",
          ],
          "title"      => [
            "label"         => "Title",
          ],
          "document_original_name"
                          => [
            "label"         => "Original name"
          ],
          "description"   => [
            "label"         => "Desciption",
          ],
          "document"      => [
            "label"         => "Document",
          ],
          "status"        => [
            "label"         => "Status",
          ],
          "published_at"  => [
            "label"         => "Published",
          ],
          "expired_at"    => [
            "label"         => "Expiring",
          ],
        ],
        "schema"        => [
          "publishing"    => [
            "label"         => "Publishing information"
          ]
        ],
      ],
    ],
    "document_category"
                    => [
      "form"          => [
        "fields"        => [
          "site"          => [
            "label"         => "Site",
          ],
          "title"         => [
            "label"         => "Title",
          ],
          "slug"          => [
            "label"         => "Slug",
          ],
          "media"         => [
            "label"         => "Media",
          ],
          "lead"          => [
            "label"         => "Lead",
          ],
          "status"        => [
            "label"       => "Status",
          ],
          "published_at"  => [
            "label"       => "Published",
          ],
          "expired_at"    => [
            "label"       => "Expiring",
          ],
        ],
        "schema"      => [
          "publishing"  => [
            "label"       => "Publishing information"
          ]
        ]
      ],
    ],
  ]
];
