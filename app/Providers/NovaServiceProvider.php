<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;

use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Dashboards\Main;
use Laravel\Nova\Menu\Menu as NovaMenu;
use Laravel\Nova\Menu\MenuItem as NovaMenuItem;
use Laravel\Nova\Menu\MenuSection as NovaMenuSection;

use App\Nova\Admin;
use App\Nova\Attribute;
use App\Nova\Brand;
use App\Nova\Category;
use App\Nova\Link;
use App\Nova\Menu;
use App\Nova\Product;
use App\Nova\Site;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    parent::boot();

    Nova::withBreadcrumbs();

    Nova::mainMenu(function (Request $request) {
      return [
        NovaMenuSection::dashboard(Main::class)->icon('chart-bar'),

        NovaMenuSection::make(__('Administer'), [
          NovaMenuItem::resource(Admin::class)
            ->canSee(function (NovaRequest $request) {
              return $request->user()->can('viewAny', \Neon\Admin\Models\Admin::class);
            }),
          NovaMenuItem::resource(Attribute::class)
            ->canSee(function (NovaRequest $request) {
              return $request->user()->can('viewAny', \Neon\Attributable\Models\Attribute::class);
            }),
        ])
          ->icon('adjustments')
          ->collapsable(),

        NovaMenuSection::make(__('Website'), [
          NovaMenuItem::resource(Site::class)
            ->canSee(function (NovaRequest $request) {
              return config('site.driver', 'file') == 'database' && $request->user()->can('viewAny', \Neon\Site\Models\Site::class);
            }),
          NovaMenuItem::resource(Menu::class)
            ->canSee(function (NovaRequest $request) {
              return $request->user()->can('viewAny', \Neon\Models\Menu::class);
            }),
          NovaMenuItem::resource(Link::class)
            ->canSee(function (NovaRequest $request) {
              return $request->user()->can('viewAny', \Neon\Models\Link::class);
            }),
        ])
          ->icon('globe')
          ->collapsable(),

        NovaMenuSection::make(__('Products'), [
          NovaMenuItem::resource(Brand::class)
            ->canSee(function (NovaRequest $request) {
              return $request->user()->can('viewAny', \App\Models\Brand::class);
            }),
          NovaMenuItem::resource(Category::class)
            ->canSee(function (NovaRequest $request) {
              return $request->user()->can('viewAny', \App\Models\Category::class);
            }),
          NovaMenuItem::resource(Product::class)
            ->canSee(function (NovaRequest $request) {
              return $request->user()->can('viewAny', \App\Models\Product::class);
            }),
        ])
          ->icon('shopping-bag'),

        NovaMenuSection::make(__('Resources'), [

          /** Here comes all the menu items...
         * 
         * ...
         * 
         * ...
         * 
         */
        ])->collapsable()
      ];
    });

    Nova::footer(function ($request) {
      return view('nova::partials.footer')->render();
    });
  }

  /**
   * Register the Nova routes.
   *
   * @return void
   */
  protected function routes()
  {
    Nova::routes()
      ->withAuthenticationRoutes()
      ->withPasswordResetRoutes()
      ->register();
  }

  /**
   * Register the Nova gate.
   *
   * This gate determines who can access Nova in non-local environments.
   *
   * @return void
   */
  protected function gate()
  {
    Gate::define('viewNova', function ($user) {
      return in_array($user->email, [
        //
      ]);
    });
  }

  /**
   * Get the dashboards that should be listed in the Nova sidebar.
   *
   * @return array
   */
  protected function dashboards()
  {
    return [
      new \App\Nova\Dashboards\Main,
    ];
  }

  /**
   * Get the tools that should be listed in the Nova sidebar.
   *
   * @return array
   */
  public function tools()
  {
    return [];
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }
}
