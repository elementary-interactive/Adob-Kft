<?php

use Filament\Support\Colors\Color;
// use Awcodes\FilamentStickyHeader\StickyHeaderPlugin;

return [

  /** Changing the Filament's path on what the admin panel is accessible.
   * 
   * @see https://filamentphp.com/docs/3.x/panels/configuration#changing-the-path
   */
  'path'      => env('NEON_ADMIN_PATH', 'admin'),

  // 'logo'      => [
  //   'view'      => 'sfsdfs.sdfsdfs',
  //   'height'    => '1.5rem'
  // ],

  'groups'    => [
    'Termékek',
    'Importok / Exportok'
  ],

  /** Set up folder for admin resources. As you generate resources, please take
   * attention, Neon Admin uses 'App\Admin\Resources' namespace for your resources,
   * so pleasespecify namespace when generates resources.
   * 
   * May you have different resources in different folders, so you can put all 
   * path here, into an array.
   * 
   * @see https://filamentphp.com/docs/3.x/panels/resources/getting-started#specifiying-a-custom-model-namespace
   */
  'resources' => [
    'Admin/Resources'
  ],

  /** By default, Filament will respond to requests from all domains. If you'd
   * like to scope it to a specific domain, you can use the domain() method,
   * similar to Route::domain() in Laravel.
   * 
   * @see https://filamentphp.com/docs/3.x/panels/configuration#setting-a-domain
   */
  'domain'    => null,

  'path'      => 'admin',

  /** Neon Admin uses separated guard for authentication and authorization.
   *
   */
  'guard'     => 'admin',

  /** Admin colors.
   * 
   * @see https://filamentphp.com/docs/3.x/panels/themes#changing-the-colors
   */
  'colors'    => [
    'primary'   => Color::Cyan,
    'danger'    => Color::Rose,
    'gray'      => Color::Gray,
    'info'      => Color::Sky,
    'success'   => Color::Emerald,
    'warning'   => Color::Orange,
  ],

  // /** Set font. You can set font-family, and also the provider, if it's needed.
  //  * 
  //  * 'font'  => [
  //  *   'font-family' => 'Inter',
  //  *   'provider'    => GoogleFontProvider::class
  //  * ]
  //  * @see https://filamentphp.com/docs/3.x/panels/themes#changing-the-font
  //  */
  // 'font'      => [
  //   'font-family' => 'Inter',
  //   'provider'    => \Filament\FontProviders\GoogleFontProvider::class
  // ],

  'plugins'   => [
    // StickyHeaderPlugin::make(),
  ],

  /** If needed to alert on living form after changes, should set this to true.
   * 
   * @see https://filamentphp.com/docs/3.x/panels/configuration#unsaved-changes-alerts
   */
  'unsaved-changes-alert' => false,
];