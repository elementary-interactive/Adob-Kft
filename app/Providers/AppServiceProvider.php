<?php

namespace App\Providers;

use Filament\Support\Assets\AlpineComponent;
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
        try {
            \DB::select("SET @@SQL_MODE = CONCAT(@@SQL_MODE, ',NO_BACKSLASH_ESCAPES')");
        }
        catch (\Exception $e)
        {
            
        }
        Relation::morphMap([
            'admin'                 => \Neon\Admin\Models\Admin::class,
            // 'admin'                => Neon\Admin\Models\Admin::class,
            // 'attibute'              => \Neon\Attributable\Models\Attribute::class,
            // 'attibute_value'        => \Neon\Attributable\Models\AttributeValue::class,
            // 'link'                  => \Neon\Models\Link::class,
            // 'menu'                  => \Neon\Models\Menu::class,
            // 'menu_item'             => \Neon\Models\MenuItem::class,
            // 'site'                  => \Neon\Site\Models\Site::class,
            // 'site_dependency'       => \Neon\Site\Models\SiteDependencies::class,
            'brand'                 => \App\Models\Brand::class,
            'brand_category_counts' => \App\Models\BrandCategoryCounts::class,
            'category'              => \App\Models\Category::class,
            'category_product'      => \App\Models\CategoryProduct::class,
            'product'               => \App\Models\Product::class,
            'product_import'        => \App\Models\ProductImport::class,
            'user'                  => \App\Models\User::class,
        ]);// 
        
        
        AlpineComponent::make('a-d-o-b-media-handler', __DIR__ . '/resources/js/components/a-d-o-b-media-handler.js');
    }
}
