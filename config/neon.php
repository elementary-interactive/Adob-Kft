<?php

use App\Nova\Flexible\Layouts\SlideshowLayout;

return [
  /**
   * ...
   */
  'menu'    => [
    'item'  => [
      'model' => \Neon\Models\MenuItem::class,
    ],
    'model' => \Neon\Models\Menu::class,
  ],

  'link'  => [
    'model' => \Neon\Models\Link::class,
  ],

  'content' => [
    // 'model' => \Neon\Models\Content::class,
    'layouts' => [
      // \App\Nova\Flexible\Layouts\NewsLayout::class,
      // \App\Nova\Flexible\Layouts\ProductsLayout::class,
      // \App\Nova\Flexible\Layouts\TitleLayout::class,
      \App\Nova\Flexible\Layouts\ContentLayout::class,
      \App\Nova\Flexible\Layouts\BrowserLayout::class,
      // \App\Nova\Flexible\Layouts\VideoLayout::class,
      // \App\Nova\Flexible\Layouts\ContentHighlightLayout::class,
      // \App\Nova\Flexible\Layouts\ShortcutsLayout::class,
      // \App\Nova\Flexible\Layouts\SlideshowLayout::class,
    ],
  ]
];