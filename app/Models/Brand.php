<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Neon\Models\Traits\Uuid;

class Brand extends Model
{
    use SoftDeletes;
    use Uuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        
    ];

    /** The attributes that should be handled as date or datetime.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];
    
    public function products(): HasMany
    {
        return $this->hasMany(\App\Models\Product::class);
    }
    
}
