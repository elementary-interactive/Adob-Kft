<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class Site extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\User>
     */
    public static $model = \Neon\Site\Models\Site::class;

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
        'locale', 'title', 'domain',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make(__('title'), 'title')
                ->sortable()
                ->rules('required', 'max:255'),
            Slug::make('', 'slug')
                ->from('title')
                ->separator('_')
                ->hideFromIndex(),
            Image::make(__('Favicon'), 'favicon')
                ->store(function (Request $request, $model) {
                    /**
                     * @todo Handle favicon via media library
                     */
                    // $model->addMediaFromRequest('logo')->toMediaCollection('manufacturers');
            
                    return true;
                }),
            KeyValue::make(__("Domains"), 'domains')
                ->rules('required'),
            Select::make(__('Locale'), 'locale')
                ->options(config('site.available_locales')),
            Textarea::make(__('Robots'), 'robots')
                ->rows(5),
            Boolean::make(__("Default site"), 'default')
                ->help(__('The domain which marked as default will be loaded if no domains were matched by domain or prefix.')),

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
}
