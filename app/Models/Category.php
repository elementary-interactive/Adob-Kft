<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Neon\Models\Traits\Uuid;
use Baum\Node;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Neon\Models\Statuses\BasicStatus;

class Category extends Node
{
    use SoftDeletes;
    use Uuid;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /** Children in a multi level navigation.
     *
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withTimestamps();
    }

    public function scopeOnlyBrand($query, Brand $brand)
    {
        // $productIds = $brand->products()->where('status', BasicStatus::Active->value)->get('id');
        $lfts = Category::with('products')->whereHas('products', function ($query) use ($brand) {
            $query
                ->where('status', BasicStatus::Active->value)
                ->whereHas('brand', function($query) use ($brand) {
                    $query->where('id', $brand->id);
                });
        })->get();

        return $query->with('products')
            ->where(function ($q) use ($lfts) {
                foreach ($lfts as $lft)
                {
                    $q->orWhereRaw("{$lft->lft} BETWEEN `lft` AND `rgt`");
                }
            });
    }

    public function getUrlAttribute(): string
    {
        return route('product.browse', [
            'slug'  => $this->getAncestorsAndSelf()->implode('slug', '/')
        ]);
    }

    public function getFullSlugAttribute(): string
    {
        return $this->getAncestorsAndSelf()->implode('slug', '/');
    }

    /** Getting "counts" attriute. This way we try to count products of the
     * subcategories, or the products related to the category itself.
     * 
     * @return int;
     */
    public function getCountsAttribute(): int
    {
        return $this->products_count ?: 666;
    }
}
