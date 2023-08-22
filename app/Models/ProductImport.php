<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Neon\Models\Traits\Statusable;
use Neon\Models\Traits\Uuid;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
    'imported_by'
  ];

  /** The attributes that should be handled as date or datetime.
   *
   * @var array
   */
  protected $dates = [
    
  ];

  /** Cast attribute to array...
   *
   */
  protected $casts = [
    'finished_at'   => 'datetime',
    'created_at'    => 'datetime',
    'updated_at'    => 'datetime',
    'deleted_at'    => 'datetime',
  ];

  /** The model's default values for attributes.
   *
   * @var array
   */
  protected $attributes = [
  ];

  public function increaseProductInserted() {}
  public function increaseProductModified() {}
}
