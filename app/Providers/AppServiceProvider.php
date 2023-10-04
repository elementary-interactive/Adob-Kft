<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'admin'                 => Neon\Admin\Models\Admin::class,
            // 'admin2'                => Neon\Admin\Models\Admin::class,
            'attibute'              => \Neon\Attributable\Models\Attribute::class,
            'attibute_value'        => \Neon\Attributable\Models\AttributeValue::class,
            'link'                  => \Neon\Models\Link::class,
            'menu'                  => \Neon\Models\Menu::class,
            'menu_item'             => \Neon\Models\MenuItem::class,
            'site'                  => \Neon\Site\Models\Site::class,
            'site_dependency'       => \Neon\Site\Models\SiteDependencies::class,
            'brand'                 => \App\Models\Brand::class,
            'brand_category_counts' => \App\Models\BrandCategoryCounts::class,
            'category'              => \App\Models\Category::class,
            'category_product'      => \App\Models\CategoryProduct::class,
            'iroduct'               => \App\Models\Product::class,
            'product_import'        => \App\Models\ProductImport::class,
            'user'                  => \App\Models\User::class,
        ]);
    }
}
