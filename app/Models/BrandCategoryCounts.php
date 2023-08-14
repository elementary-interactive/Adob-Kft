<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Neon\Models\Traits\Uuid;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandCategoryCounts extends Model
{
    use Uuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'brand_id',
        'category_id',
        'counts'
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand ::class);
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
