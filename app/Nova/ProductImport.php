<?php

namespace App\Nova;

use App\Nova\Admin as NovaAdmin;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Status;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Http\Requests\NovaRequest;
use Neon\Admin\Models\Admin;

class ProductImport extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ProductImport>
     */
    public static $model = \App\Models\ProductImport::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'created_at';

    /**
     * Indicates if the resource should be globally searchable.
     *
     * @var bool
     */
    public static $globallySearchable = false;

    /**
     * Indicates whether the resource should automatically poll for new resources.
     *
     * @var bool
     */
    public static $polling = true;

    /**
     * The interval at which Nova should poll for new resources.
     *
     * @var int
     */
    public static $pollingInterval = 10;

    /**
     * The default shorting field.
     *
     * @var string
     */
    public static $defaultSort = 'created_at';

    /**
     * The default shorting direction.
     *
     * @var string
     */
    public static $defaultDir = 'desc';

    /**
     * Indicates whether to show the polling toggle button inside Nova.
     *
     * @var bool
     */
    public static $showPollingToggle = true;

    public static function label()
    {
        return __('Imports');
    }

    public static function singularLabel()
    {
        return __('Import');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Status::make('', 'status')
                ->loadingWhen(['waiting', 'running'])
                ->failedWhen(['failed']),
            Number::make(__('Products inserted'), 'products_inserted'),
            Number::make(__('Products modified'), 'products_modified'),

            Number::make(__('Categories inserted'), 'categories_inserted'),
            Number::make(__('Categories modified'), 'categories_modified'),

            Number::make(__('Brands inserted'), 'brands_inserted'),
            Number::make(__('Brands modified'), 'brands_modified'),

            Number::make(__('Fails counter'), 'fails_counter'),

            Code::make('Log', 'data')
                ->hideFromIndex()
                ->showOnDetail()
                ->json(),

            DateTime::make(__('Started at'), 'created_at')
                ->sortable(),
            DateTime::make(__('Finished at'), 'finished_at'),

            BelongsTo::make(__('Importer'), 'imported_by', NovaAdmin::class),
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
        if (static::$defaultSort && empty($request->get('orderBy'))) {
            $query->getQuery()->orders = [];
            return $query->orderBy(static::$defaultSort, static::$defaultDir);
        }
        return $query;
    }
}
