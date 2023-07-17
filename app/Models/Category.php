<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Neon\Models\Traits\Uuid;
use Baum\Node;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function getUrlAttribute(): string
    {
        return route('product.browse', [
            'slug'  => $this->getAncestorsAndSelf()->implode('slug', '/')
        ]);
    }

    /** Getting "counts" attriute. This way we try to count products of the
     * subcategories, or the products related to the category itself.
     * 
     * @return int;
     */
    public function getCountsAttribute(): int
    {
        return $this->descendants()->withCount('products')->get()->count() ?: $this->products()->count();
    }
}
