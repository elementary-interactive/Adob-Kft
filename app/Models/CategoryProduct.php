<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class CategoryProduct extends Pivot implements Sortable
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
      'sort_on_belongs_to'    => true,
  ];

  // /** Extending the boot, to be able to set Observer this model, as because
  //  * the Observer will not run on the inherited classes.
  //  *
  //  * @see https://github.com/laravel/framework/issues/25546
  //  * @see https://laravel.com/docs/6.x/eloquent#global-scopes
  //  */
  // protected static function boot()
  // {
  //   /** We MUST call the parent boot method  in this case the:
  //    *      \Illuminate\Database\Eloquent\Model
  //    */
  //   parent::boot();

  //   static::saving(function ($model) {
  //     /** Handling URL field: slug is only for the given link, the URL will
  //      * contain all the generated slugs.
  //      *
  //      */
  //     $result = DB::select('select count(product_id) as counter from category_product where category_id = ?', [$model->id]);
  //     $model->{$model->sortable['order_column_name']} = $result['counter'];
  // });
  // }

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
