<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\{
    BooleanGroup,
    KeyValue,
    MorphTo,
    Select,
    Slug,
    Text
};

class Attribute extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Neon\Attributable\Models\Attribute::class;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    // public static $group = 'Adminisztráció';

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
        'name',
    ];

    public static function label()
    {
        return __('Variables');
    }

    public static function singularLabel()
    {
        return __('Variable');
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
            Text::make('Név', 'name')
                ->rules('required', 'max:255'),
            Slug::make('', 'slug')
                ->from('name')
                ->separator('_'),
            Text::make(__('Validation rules'), 'rules')
                ->help('A szabályokat a keretrendszer <a href="https://laravel.com/docs/10.x/validation#available-validation-rules" target="_blank">beviteli szabályai</a> szerint kell megadni.'),
            Select::make(__('Form field'), 'field')
                ->options(config('attributable.fields'))
                ->help(__('Attribute value edit will shown with this form field.')),
            Select::make(__('Cast as'), 'cast_as')
                ->options(config('attributable.casts')),
            Select::make(__('Scope'), 'class')
                ->options(config('attributable.scopes'))
                ->help(__('Only on this resource will this attribute value available.')),
            KeyValue::make('parameters')
                ->rules('json'),
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
        /** If the request is related to *something* we try to get the type of
         * the given resource.
         */
        if ($request->get('viaResource'))
        {
            /** Name of the class to the variable's value should be related.
             * @var string
             */
            $resource_class = '\\App\\Nova\\'.\Str::ucfirst(\Str::singular($request->get('viaResource')));
            $resource = $resource_class::$model;
                    
            /** Querying only for the scope's variables. */
            $query->where('class', $resource);
        }

        return parent::relatableQuery($request, $query);
    }
}
