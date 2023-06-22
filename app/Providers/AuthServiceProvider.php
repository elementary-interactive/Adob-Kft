<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \Neon\Admin\Models\Admin::class     => \App\Policies\AdminPolicy::class,
        \Neon\Site\Models\Site::class       => \App\Policies\SitePolicy::class,
        \Neon\Models\Menu::class            => \App\Policies\MenuPolicy::class,
        \Neon\Models\Link::class            => \App\Policies\LinkPolicy::class,
        \App\Models\Brand::class            => \App\Policies\BrandPolicy::class,
        \App\Models\Product::class          => \App\Policies\ProductPolicy::class,
        \App\Models\Category::class         => \App\Policies\CategoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
