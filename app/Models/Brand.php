<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Neon\Models\Traits\Uuid;
use Neon\Models\Traits\Statusable;

class Brand extends Model
{
  use SoftDeletes;
  use Statusable;
  use Uuid;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name', 'slug', 'status', 'is_featured'
  ];

  /** Casts
   *
   * @var array
   */
  protected $casts = [
    'is_featured' => 'boolean'
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

  protected static function boot()
  {
    /** We MUST call the parent boot method  in this case the:
     *      \Illuminate\Database\Eloquent\Model
     */
    parent::boot();

    static::saved(function ($model) {
      // CountBrandCategoryProducts::dispatch();
    });
  }
}
