<?php

namespace App\Models;

use App\Jobs\ADOBImagesImportBatch_new;
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

class ImageImport extends Model
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
    'images',
    'batch_id',
    'imported_by_id',
  ];

  /** Cast attribute to array...
   *
   */
  protected $casts = [
    'data'          => 'json',
    'file'          => 'string',
    'job'           => 'string',
    'images'        => 'array',
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
    'data'                => '',
    'job'                 => '',
  ];

  public static function boot()
  {
    parent::boot();

    self::created(function ($model) {
      $model->status           = 'running';
      $model->records_counter  = count($model->images);
      $model->imported_by_id   = auth()->user()->id;
      $model->save();

      ADOBImagesImportBatch_new::dispatch($model);
    });

    self::updating(function ($model) {
      if ($model->records_counter  == $model->records_handled)
      {
        $model->status        = 'finished';
        $model->finished_at   = now();
      }
    });
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

  public function imported_by(): BelongsTo
  {
    return $this->belongsTo(Admin::class);
  }
}
