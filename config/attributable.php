<?php

use Neon\Models\Link;
use Neon\Models\Menu;
use Neon\Site\Models\Site;

return [

  /** IMPORTANT!!!
   * 
   * To use Attributable cache, we need cache engine what can work with tagging.
   * 
   * @see https://laravel.com/docs/10.x/cache#cache-tags
   * 
   * If cache is turned on you can use the next command to empty:
   * ```
   *   php artisan attributes:clear
   * ```
   */
  'cache' => env('NEON_ATTRIBUTABLE_CACHE', false),

  'scopes' => [
    Site::class => 'Weboldal',
    Link::class => 'Oldal',
    Menu::class => 'Menü'
  ],
  'fields' => [
      'Text'  => 'Szöveges beviteli mező'
  ],
  'casts' => [
    'string'  => 'casts.string',
    'int'     => 'casts.int',
    'boolean' => 'casts.boolean'
  ]

];