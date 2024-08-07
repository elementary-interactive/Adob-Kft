<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
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
    'brands_inserted',
    'brands_modified',
    'categories_inserted',
    'categories_modified',
    'products_inserted',
    'products_modified',
    'fails_counter',
    'records_counter',
    'data',
    'file',
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
    'batch'         => 'array',
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
    'records_counter'     => 0,
    'brands_inserted'     => 0,
    'brands_modified'     => 0,
    'categories_inserted' => 0,
    'categories_modified' => 0,
    'products_inserted'   => 0,
    'products_modified'   => 0,
    'fails_counter'       => 0,
    'data'                => '',
    'job'                 => '',
    'batch'               => '',
  ];

  protected $key  = null;
  /**
   * The "booted" method of the model.
   *
   * @return void
   */
  protected static function boot()
  {
    parent::boot();

    static::created(function ($model) {
      Cache::add($model->id . '_brands_inserted', 0, now()->addHours(4));
      Cache::add($model->id . '_brands_modified', 0, now()->addHours(4));
      Cache::add($model->id . '_categories_inserted', 0, now()->addHours(4));
      Cache::add($model->id . '_categories_modified', 0, now()->addHours(4));
      Cache::add($model->id . '_products_inserted', 0, now()->addHours(4));
      Cache::add($model->id . '_products_modified', 0, now()->addHours(4));
    });

    static::retrieved(function ($model) {
      $model->batch = json_decode($model->batch);
    });

    static::saving(function ($model)
    {
      $fails = json_decode(Cache::get($model->id . '_fails')) ?: [];

      $model->brands_inserted = Cache::get($model->id . '_brands_inserted', 0);
      $model->brands_modified = Cache::get($model->id . '_brands_modified', 0);
      $model->categories_inserted = Cache::get($model->id . '_categories_inserted', 0);
      $model->categories_modified = Cache::get($model->id . '_categories_modified', 0);
      $model->products_inserted = Cache::get($model->id . '_products_inserted', 0);
      $model->products_modified = Cache::get($model->id . '_products_modified', 0);
      $model->fails_counter = count($fails);
      $model->data = json_encode([
        'fails'         => $fails,
      ]);

      if (!is_string($model->batch)) {
        $model->batch = json_encode($model->batch);
      }
    });
  }

  // public function __construct()
  // {
  //   // parent::__construct();

  //   // $this->id = (string) Str::uuid();

  //   // // Initialize the cache keys
  //   // Cache::add($this->id . '_brands_inserted', 0, now()->addHours(4));
  //   // Cache::add($this->id . '_brands_modified', 0, now()->addHours(4));
  //   // Cache::add($this->id . '_categories_inserted', 0, now()->addHours(4));
  //   // Cache::add($this->id . '_categories_modified', 0, now()->addHours(4));
  //   // Cache::add($this->id . '_products_inserted', 0, now()->addHours(4));
  //   // Cache::add($this->id . '_products_modified', 0, now()->addHours(4));
  //   // Cache::add($this->id . '_fails', json_encode([]), now()->addHours(4));
  // }

  public function addFail($message)
  {
    $data = json_decode(Cache::add($this->id . '_fails'));
    $data[] = $message;

    Cache::put($this->id . '_fail', json_encode($data));

    // $this->attributes['data'] = json_encode($data);

    // $this->attributes['fails_counter']++;
    // $this->save();
  }

  public function increaseBrandInserted()
  {
    //   $this->attributes['brands_inserted']++;
    //   $this->save();
    Cache::increment($this->id . '_brands_inserted');
  }

  public function increaseBrandModified()
  {
    // $this->attributes['brands_modified']++;
    // $this->save();
    Cache::increment($this->id . '_brands_modified');
  }

  public function increaseCategoryInserted()
  {
    Cache::increment($this->id . '_categories_inserted');
  }

  public function increaseCategoryModified()
  {
    Cache::increment($this->id . '_categories_modified');
  }

  public function increaseProductInserted()
  {
    Cache::increment($this->id . '_products_inserted');
  }

  public function imported_by(): BelongsTo
  {
    return $this->belongsTo(Admin::class);
  }
}
