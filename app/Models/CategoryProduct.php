<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class CategoryProduct extends Pivot
{
  use SoftDeletes;
  use SortableTrait;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'order',
  ];

  /** The attributes that should be handled as date or datetime.
   *
   * @var array
   */
  protected $dates = [
    'created_at',
    'updated_at',
    'deleted_at',
  ];

  /** Set up sorting.
   *
   * @var array
   */
  public $sortable = [
      'order_column_name'     => 'order',
      'sort_when_creating'    => true,
      'sort_on_has_many'      => true,
  ];

  /** Extending the boot, to be able to set Observer this model, as because
   * the Observer will not run on the inherited classes.
   *
   * @see https://github.com/laravel/framework/issues/25546
   * @see https://laravel.com/docs/6.x/eloquent#global-scopes
   */
  protected static function boot()
  {
    /** We MUST call the parent boot method  in this case the:
     *      \Illuminate\Database\Eloquent\Model
     */
    parent::boot();
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class);
  }
  
  public function buildSortQuery()
  {
      return static::query()
          ->where('category_id', $this->category_id);
  }
}
