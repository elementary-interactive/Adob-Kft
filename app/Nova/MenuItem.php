<?php

namespace App\Nova;


use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

use Outl1ne\NovaSortable\Traits\HasSortableRows;

/** Nova fields.
 * 
 */
use Laravel\Nova\Fields\{
    BelongsTo,
    Select,
    Text,
};
class MenuItem extends Resource
{
    use HasSortableRows;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Neon\Models\MenuItem::class;

    /**
     * The visual style used for the table. Available options are 'tight' and 'default'.
     *
     * @var string
     */
    public static $tableStyle = 'tight';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * Disable sorting cache.
     * 
     * @var boolean
     */
    public static $sortableCacheEnabled = false;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title', 'url'
    ];

    public static function label()
    {
        return __('Menu Items');
    }

    public static function singularLabel()
    {
        return __('Menu Item');
    }

    /**
     * Return the location to redirect the user after creation.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return \Laravel\Nova\URL|string
     */
    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return '/resources/menus/' . $resource->menu_id;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $model = $this;

        $fields = [
            BelongsTo::make(__('Menu'), 'menu', \App\Nova\Menu::class),
            BelongsTo::make(__('Link'), 'link', \App\Nova\Link::class),
            Text::make(__('Title'), 'title')
                ->rules('required', 'max:255'),
            Text::make(__('URL'), 'url')
                ->rules('max:255')
                ->help(__('Advanced URL for the menu item. If not set, the link\'s URL will be inherited.')),
            Select::make(__('Link open target'), 'target')
                ->options([
                    '_blank'    => __('New window'),
                    '_self'     => __('Same window'),
                ])
                ->hideFromIndex()
                ->hideFromDetail(),
        ];

        return $fields;
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
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

        $next->getQuery()->orders = [];
        $next->orderBy(
            'order',
            'asc'
        );

        // dd($next);
        return $next;
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        $next = parent::relatableQuery($request, $query);
        
        $next->withoutGlobalScopes([
            \Neon\Models\Scopes\ActiveScope::class
        ]);

        return $next;
    }
}
