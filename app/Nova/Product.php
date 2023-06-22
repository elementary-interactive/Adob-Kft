<?php

namespace App\Nova;

use App\Models\Product as ModelsProduct;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Neon\Models\Statuses\BasicStatus;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

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
        'name', 'product_id', 'description'
    ];

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
            BelongsTo::make(__('Brand'), 'brand', Brand::class)
                ->sortable()
                ->rules(['required', 'max:255']),
            Text::make(__('Name'), 'name')
                ->sortable()
                ->rules(['required', 'max:255']),
            Slug::make(__('Brand URI'), 'slug')
                ->rules(['required', Rule::unique('products', 'slug')->ignore($model->id, 'id')])
                ->from('name'),
            Text::make(__('Product ID'), 'product_id')
                ->rules(['required', Rule::unique('products', 'product_id')->ignore($model->id, 'id')])
                ->required(),
            Text::make(__('EAN code'), 'ean')
                ->rules(['required', Rule::unique('products', 'ean')->ignore($model->id, 'id'), 'max:13'])
                ->required(),
            Text::make(__('Product Number'), 'product_number')
                ->rules(['nullable']),
            Trix::make(__('Description'), 'description'),
            Trix::make(__('Package'), 'packaging'),
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
                ->min(1)->max(10000000)->step(0.01)
                ->currency('HUF')
                ->help(__('Informative, recommended net consumer price.')),
            Boolean::make(__('Is on sale'), 'on_sale'),
            BelongsToMany::make(__('Categories'), 'categories', Category::class)
                ->fields(new CategoryProductFields),
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
        return [];
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
        return [];
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
