<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Neon\Models\Traits\Statusable;
use Neon\Models\Traits\Uuid;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Neon\Admin\Models\Admin;

class ProductImport extends Model
{
  use SoftDeletes;
  use Uuid;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'data',
    'brands_inserted',
    'brands_modified',
    'categories_inserted',
    'categories_modified',
    'products_inserted',
    'products_modified',
    'data'
  ];

  /** Cast attribute to array...
   *
   */
  protected $casts = [
    'data'          => 'json',
    'finished_at'   => 'datetime',
    'created_at'    => 'datetime',
    'updated_at'    => 'datetime',
    'deleted_at'    => 'datetime',
    'finished_at'   => 'datetime',
  ];

  /**
   * The model's default values for attributes.
   *
   * @var array
   */
  protected $attributes = [
    'status'              => 'waiting',
    'brands_inserted'     => 0,
    'brands_modified'     => 0,
    'categories_inserted' => 0,
    'categories_modified' => 0,
    'products_inserted'   => 0,
    'products_modified'   => 0,
    'data'                => ''
  ];

  public function addFail($message)
  {
    $data = json_decode($this->attributes['data']);
    if (!is_array($data))
    {
      $data = [];
    }
    if (!array_key_exists('fails', $data))
    {
      $data['fails'] = [];
    }
    $data['fails'][] = $message;

    $this->attributes['data'] = json_encode($data);
  }

  public function increaseBrandInserted()
  {
    $this->attributes['brands_inserted']++;
  }

  public function increaseBrandModified()
  {
    $this->attributes['brands_modified']++;
  }

  public function increaseCategoryInserted()
  {
    $this->attributes['categories_inserted']++;
  }

  public function increaseCategoryModified()
  {
    $this->attributes['categories_modified']++;
  }

  public function increaseProductInserted()
  {
    $this->attributes['products_inserted']++;
  }

  public function increaseProductModified()
  {
    $this->attributes['products_modified']++;
  }

  public function imported_by(): BelongsTo
  {
    return $this->belongsTo(Admin::class);
  }
 
}
