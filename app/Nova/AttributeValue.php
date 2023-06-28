<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\{
    BelongsTo,
    BooleanGroup,
    Date,
    Heading,
    KeyValue,
    MorphTo,
    Select,
    Slug,
    Text
};
use Neon\Attributable\Models\Attribute;
use Neon\Site\Site;

class AttributeValue extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Neon\Attributable\Models\AttributeValue::class;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    // public static $group = 'Adminisztráció';

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

    public function title()
    {
        return $this->resource->attribute->name;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        // dump($request->viaResourceId);
        $model = $this;

        $fields = [];

        if ($request->editMode == 'create')
        {
            if ($request->has('viaResourceId'))
            {
                $fields[] = MorphTo::make(__('Connected Object'), 'attributable')->types([ 
                        \App\Nova\Site::class
                    ])
                    ->withMeta([
                        'moprhToId' => $request->viaResourceId
                    ])
                    ->readonly();
            } else {
                $fields[] = MorphTo::make(__('Connected Object'), 'attributable')->types([ 
                    \App\Nova\Site::class
                ]);
            }
            if (!$this->resource->exists) {
                $fields[] = BelongsTo::make('attribute');
            }
        }
        else if ($request->editMode == 'update')
        {
            $field = '\Laravel\Nova\Fields\\'.$this->resource->attribute->field;
            $fields[] = $field::make(
                $this->resource->attribute->name,
                'value')
                ->rules($this->resource->attribute->rules);
            $fields[] = Date::make(__('Published at'), 'published_at');
            $fields[] = Date::make(__('Expired at'), 'expired_at');
        } else {
            $fields[] = Text::make(__('Value'), 'value');
        }

        // if ($request->has('viaResource'))
        // {
        //     $fields[] = Attribute::where()
        // } else {
            
        // }


    //     $fields = [
    //         Text::make('Név', 'name')
    //             ->rules('required', 'max:255'),
    //         Slug::make('', 'slug')
    //             ->from('name')
    //             ->hideFromIndex()
    //             ->hideFromDetail(),
    //         Text::make(__('Validation rules'), 'rules')
    //             ->help('A szabályokat a keretrendszer <a href="https://laravel.com/docs/6.x/validation#available-validation-rules" target="_blank">beviteli szabályai</a> szerint kell megadni.'),
    //         Select::make(__('Form field'), 'field')
    //             ->options(config('attributable.fields')),
    //         Select::make(__('Cast as'), 'cast_as')
    //             ->options(config('attributable.casts')),
    //         Select::make(__('Scope'), 'class')
    //             ->options(config('attributable.scopes')),
    //         KeyValue::make('parameters')
    //             ->rules('json'),
    //     ];

        return $fields;
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
        return '/resources/'.static::uriKey().'/'.$resource->getKey().'/edit';
    }

    /**
     * Return the location to redirect the user after creation.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return \Laravel\Nova\URL|string
     */
    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        dd($resource, $request);
        // return '/resources/'..'/'.$resource->attributable_id;
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
