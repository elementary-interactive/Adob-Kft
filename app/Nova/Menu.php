<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

/** Nova fields.
 * 
 */
use Laravel\Nova\Fields\{
    Badge,
    BelongsTo,
    BelongsToMany,
    Boolean,
    HasMany,
    Slug,
    Text,
};

class Menu extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Neon\Models\Menu::class;

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
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title',
    ];

    public static function label()
    {
        return __('Menus');
    }

    public static function singularLabel()
    {
        return __('Menu');
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
            BelongsToMany::make(__('Site'), 'site', \App\Nova\Site::class)
                ->fields(function ($request, $relatedModel) {
                    return [
                        Text::make(__('Dependence type'), 'dependence_type')
                            ->default(\Neon\Models\Menu::class)
                            ->readonly()
                            ->hideFromIndex(),
                    ];
                }),
            Text::make(__('Title'), 'title')
                ->rules('required', 'max:255'),
            Slug::make('', 'slug')
                ->from('title')
                ->hideFromIndex()
                ->hideFromDetail(),
                Text::make(__('Usage'), function () use ($model) {
                    return "<x-neon-menu id=\"{$model->slug}\">\n\r
                                <x-slot:tools>\n\r
                                    ...\n\r
                                </x-slot>\n\r
                            </x-neon-menu>";
            })
                ->showOnDetail(),
            Boolean::make(__('Active'), 'status')
                ->trueValue(\Neon\Models\Statuses\BasicStatus::Active->value)
                ->falseValue(\Neon\Models\Statuses\BasicStatus::Inactive->value),
            HasMany::make(__('Items'), 'items', \App\Nova\MenuItem::class)
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
            \Neon\Models\Scopes\ActiveScope::class,
            \Neon\Site\Models\Scopes\SiteScope::class
        ]);

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
            \Neon\Models\Scopes\ActiveScope::class,
            \Neon\Site\Models\Scopes\SiteScope::class,
        ]);

        return $next;
    }
}
