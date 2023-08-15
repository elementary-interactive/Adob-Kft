<?php

namespace App\Nova;

use App\Models\Product as ModelsProduct;
use App\Nova\Filters\ProductAvailable;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Neon\Models\Statuses\BasicStatus;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
class Product extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Product>
     */
    public static $model = \App\Models\Product::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name', 'slug', 'product_id', 'product_number', 'ean', 'description', 'packaging', 'price'
    ];

    public static function label()
    {
        return __('Products');
    }

    public static function singularLabel()
    {
        return __('Product');
    }

    /**
     * Return a replicated resource.
     *
     * @return static
     *
     * @throws \InvalidArgumentException
     */
    public function replicate()
    {
        return tap(parent::replicate(), function ($resource) {
            $model = $resource->model();

            $model->name        = ModelsProduct::COPY_TAG . $model->name;
            $model->product_id  = ModelsProduct::COPY_TAG . $model->product_id;
            $model->status      = BasicStatus::Inactive->value;
        });
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        $model = $this;

        return [
            Text::make(__('Product ID'), 'product_id')
                ->rules(['required', Rule::unique('products', 'product_id')->ignore($model->id, 'id')])
                ->readonly($this->id)
                ->copyable()
                ->sortable()
                ->required(),
            BelongsTo::make(__('Brand'), 'brand', Brand::class)
                ->sortable()
                ->rules(['required', 'max:255'])
                ->hideFromIndex(),
            Text::make('')
                ->resolveUsing(function () {
                    return '<a style="color: inherit;" href="'.route('product.show', ['slug' => $this->resource->slug]).'" target="_blank" title="'.$this->resource->name.'">'.view('nova::icon.svg-link', [
                        'color'     => 'rgb(var(--colors-gray-400), 0.75)'
                    ])->render().'</a>';
                })
                ->asHtml()
                ->onlyOnIndex(),
            Text::make(__('Name'), 'name')
                ->sortable()
                ->rules(['required', 'max:255']),
            Slug::make(__('Product URI'), 'slug')
                ->rules(['required', Rule::unique('products', 'slug')->ignore($model->id, 'id')])
                ->from('name')
                ->onlyOnForms(),
            Images::make(__('Images'), ModelsProduct::MEDIA_COLLECTION) // second parameter is the media collection name
                ->conversionOnPreview('thumb') // conversion used to display the "original" image
                ->conversionOnDetailView('thumb') // conversion used on the model's view
                ->conversionOnIndexView('thumb') // conversion used to display the image on the model's index page
                ->conversionOnForm('thumb') // conversion used to display the image on the model's form
                // validation rules for the collection of images
                ->singleImageRules('dimensions:min_width=100')
                ->withResponsiveImages(),
            Trix::make(__('Description'), 'description')
                ->hideFromIndex(),
            Trix::make(__('Package'), 'packaging')
                ->hideFromIndex(),
            Boolean::make(__('Available'), 'status')
                ->trueValue(\Neon\Models\Statuses\BasicStatus::Active->value)
                ->falseValue(\Neon\Models\Statuses\BasicStatus::Inactive->value)
                ->rules([Rule::excludeIf(Str::startsWith($request->product_id, \App\Models\Product::COPY_TAG))]),
            Heading::make(__('SEO and social sharing information')),
            KeyValue::make(__('Social Share data'), 'og_data'),
            KeyValue::make(__('SEO data'), 'meta_data')
                ->withMeta([
                    'keys' => ['tite', 'keywords', 'description']
                ]),
            Heading::make(__('Price information')),
            Currency::make(__('Price'), 'price')
                ->min(1)->max(10000000)->step('any')
                ->currency('HUF')
                ->locale('hu')
                ->help(__('Informative, recommended net consumer price.')),
            Boolean::make(__('Is on sale'), 'on_sale'),
            BelongsToMany::make(__('Categories'), 'categories', Category::class)
                ->fields(new CategoryProductFields),
            Text::make(__('EAN code'), 'ean')
                ->rules(['required', Rule::unique('products', 'ean')->ignore($model->id, 'id'), 'max:13'])
                ->readonly($this->id)
                ->hideFromIndex()
                ->required(),
            Text::make(__('Product Number'), 'product_number')
                ->readonly($this->id)
                ->hideFromIndex()
                ->rules(['nullable']),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [
            (new ProductAvailable),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            (new \App\Nova\Actions\ImportCategoryProduct())
                ->onlyOnIndex()
                ->standalone()
        ];
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        $next = parent::indexQuery($request, $query);

        $next->withoutGlobalScopes([
            \Neon\Models\Scopes\ActiveScope::class
        ]);

        // dd($next);
        return $next;
    }
}
