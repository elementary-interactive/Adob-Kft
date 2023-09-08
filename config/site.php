<?php

return [

  /** IMPORTANT!!!
   * 
   * Sites, to make you project fast and easy to run is hardly caching the sites' configuration even
   * if its given in the config file or in the database. Take care of change this file, and always
   * run the next command:
   * ```
   *   php artisan site:clear
   * ```
   * This will prune all sites caches.
   */

  /** 
   * The driver of the sites' config. This controls the default site "driver"
   * will be used on request. By default it's "file" what means, we will use
   * this certain file to indentify sites where the project could be run.
   * 
   * Supported: "file", "database"
   */
  'driver' => env('SITE_DRIVER', 'database'),

  'cache' => false,

  /**
   * The model what will represent a site.
   */
  'model' => \Neon\Site\Models\Site::class,

    /**
   * List of the sites.
   * - The site's ID will be used as primary key value for site related contents.
   * - Possible arguments:
   *    - domains: List of strings. On these sites we'll use the givem config.
   *    - locale: The language (locale) identifier of the given site.
   *    - default: Boolean. If no sites could be identified by domain, then we'll select by this.
   * 
   * To have the SITE_ID value in the .env file, please run:
   * ```
   *   php artisan site:create
   * ```
   */
  'hosts' => [
    env('NEON_SITE_ID') => [
      'domains' => ["/(.*)/"],
      'locale'  => 'en',
      'default' => true
    ],
    'dev' => [
      'domains' => []
    ]
  ],

  'available_locales' => [
    'hu'  => ['hu-HU', 'magyar', 'hungarian'],
    'en'  => ['en-EN', 'english', 'english'],
  ],
];