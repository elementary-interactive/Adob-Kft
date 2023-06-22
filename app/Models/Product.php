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

class Product extends Model
{
    use HasFactory;
    use Statusable;
    use SoftDeletes;
    use Uuid;

    const COPY_TAG = '[MÁSOLAT] ';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

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
        'meta_data' => 'array'
    ];

    /** The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'og_data'    => "{
            'type'          : '',
            'title'         : ''
        }",
        'meta_data'  => "{
            'title': '',
            'keywords': '',
            'description': ''
        }"
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
            if (Str::startsWith($model->product_id, self::COPY_TAG) && ($model->status === BasicStatus::Active))
            {
                throw ValidationException::withMessages(["status" => 'Másolat állapotban a termék nem aktiválható!']);
                return false;
            } 
        });
    }

    // public function __construct()
    // {
    //     parent::__construct();

    //     $this->
    // }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Brand::class);
    }


    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}
