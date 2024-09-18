<?php

namespace App\Models;

use function Illuminate\Events\queueable;
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

class ProductExport extends Model
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
    'file',
    'exported_by_id',
    'records_counter',
    'fails_counter'
  ];

  /** Cast attribute to array...
   *
   */
  protected $casts = [
    'data'          => 'json',
    'file'          => 'string',
    'job'           => 'string',
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
    'records_counter'     => 0,
    'fails_counter'       => 0,
    'data'                => '',
    'job'                 => '',
  ];

  public function getFileAttribute(): string
  {
    if (!array_key_exists('file', $this->attributes) || is_null($this->attributes['file']))
    {
      $this->attributes['file'] = 'ADOBProductsExport_' . $this->created_at->format('Ymd_His').'.xlsx';
    }

    return $this->attributes['file'];

  }

  public function addFail($message)
  {
    $data = json_decode($this->attributes['data']);
    if (!is_array($data)) {
      $data = [];
    }
    if (!array_key_exists('fails', $data)) {
      $data['fails'] = [];
    }
    $data['fails'][] = $message;

    $this->attributes['data'] = json_encode($data);

    $this->attributes['fails_counter']++;
    $this->save();
  }

  public function exported_by(): BelongsTo
  {
    return $this->belongsTo(Admin::class);
  }
}
