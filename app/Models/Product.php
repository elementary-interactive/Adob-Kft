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
use Neon\Models\Statuses\BasicStatus;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Jobs\CountBrandCategoryProducts;
use Illuminate\Support\Facades\Artisan;

class Product extends Model implements HasMedia
{
  use HasFactory;
  use Statusable;
  use SoftDeletes;
  use Uuid;
  use InteractsWithMedia;

  const COPY_TAG          = '[MÁSOLAT] ';
  const MEDIA_COLLECTION  = 'product_images';
  const MEDIA_MAIN        = 'product_main';

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'product_id', 'product_number', 'name', 'slug', 'packaging', 'description',
    'ean', 'price', 'on_sale', 'status'
  ];

  /** The attributes that should be handled as date or datetime.
   *
   * @var array
   */
  protected $dates = [
    'created_at', 'updated_at', 'deleted_at',
  ];

  /** Cast attribute to array...
   *
   */
  protected $casts = [
    'og_data'   => 'array',
    'meta_data' => 'array',
    'price'     => 'integer',
    'is_active' => 'boolean'
  ];

  /** The model's default values for attributes.
   *
   * @var array
   */
  protected $attributes = [
    'og_data'    => '{
            "type"          : "",
            "title"         : ""
        }',
    'meta_data'  => '{
            "title"         : "",
            "keywords"      : "",
            "description"   : ""
        }'
  ];

  protected static function boot()
  {
    /** We MUST call the parent boot method  in this case the:
     *      \Illuminate\Database\Eloquent\Model
     */
    parent::boot();

    static::saving(function ($model) {
      /** Handling URL field: slug is only for the given link, the URL will
       * contain all the generated slugs.
       *
       */
      if (Str::startsWith($model->product_id, self::COPY_TAG) && ($model->status === BasicStatus::Active)) {
        throw ValidationException::withMessages(["status" => 'Másolat állapotban a termék nem aktiválható!']);
        return false;
      }
    });

    static::saved(function ($model) {
      // CountBrandCategoryProducts::dispatch();
    });

    static::deleting(function($model) {
      $model->categories()->detach();
    });
  }

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection(self::MEDIA_MAIN)->singleFile();
    $this->addMediaCollection(self::MEDIA_COLLECTION);
  }

  public function registerMediaConversions(Media $media = null): void
  {
    $this->addMediaConversion('thumb')
      ->height(100)
      ->fit(Manipulations::FIT_MAX, 100, 100)
      ->optimize()
      // ->setManipulations(['h' => 100, 'fm' => 'png', 'fit' => 'max'])
      ->performOnCollections(self::MEDIA_COLLECTION)
      ->queued();

    $this->addMediaConversion('medium')
      ->height(600)
      ->fit(Manipulations::FIT_MAX, 600, 600)
      ->optimize()
      // ->setManipulations(['h' => 600, 'fit' => 'max'])
      ->performOnCollections(self::MEDIA_COLLECTION)
      ->queued();

    $this->addMediaConversion('thumb')
      ->height(100)
      ->fit(Manipulations::FIT_MAX, 100, 100)
      ->optimize()
      // ->setManipulations(['h' => 100, 'fm' => 'png', 'fit' => 'max'])
      ->performOnCollections(self::MEDIA_MAIN)
      ->queued();

    $this->addMediaConversion('medium')
      ->height(600)
      ->fit(Manipulations::FIT_MAX, 600, 600)
      ->optimize()
      // ->setManipulations(['h' => 600, 'fit' => 'max'])
      ->performOnCollections(self::MEDIA_MAIN)
      ->queued();
  }

  public function getImagesAttribute()
  {
    $media = $this->getMedia(self::MEDIA_COLLECTION);
    $result = [];

    foreach ($media as $medium)
    {
      $result[] = $medium->getUrl('thumb');
    }

    return $result;
  }

  // public function setStatusAttribute($attribute)
  // {
  //   if (is_string($attribute))
  //   {
  //     switch ($attribute)
  //     {
  //       case BasicStatus::Active->value: 
  //         $this->attributes['status'] = BasicStatus::Active;
  //         break;
  //       case BasicStatus::Inactive->value:
  //         $this->attributes['status'] = BasicStatus::Inactive;
  //         break;
  //       case BasicStatus::New->value:
  //         $this->attributes['status'] = BasicStatus::New;
  //         break;
  //     }
  //   }
  // }

  public function getIsActiveAttribute(): bool
  {
    return $this->status == BasicStatus::Active;
  }

  public function scopeOnlyBrand($query, Brand $brand)
  {
    return $query->whereHas('brand', function ($query) use ($brand) {
      $query->where('id', $brand->id);
    });
  }

  public function brand(): BelongsTo
  {
    return $this->belongsTo(\App\Models\Brand::class);
  }

  public function categories(): BelongsToMany
  {
    return $this->belongsToMany(Category::class)
      ->withTimestamps()
      ->withPivot(['order'])
      ->orderBy('order');
  }
}
