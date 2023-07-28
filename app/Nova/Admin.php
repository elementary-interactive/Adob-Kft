<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

use Laravel\Nova\Fields\
{
    Password,
    Text
};

class Admin extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Neon\Admin\Models\Admin::class;

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
        'name', 'email',
    ];

    public static function label()
    {
        return __('Administrators');
    }

    public static function singularLabel()
    {
        return __('Administrator');
    }

    /**
     * The icon of the resource.
     *
     * @var string
     */
    public static function icon()
    {
        // return view('nova::icon.svg-lock-open-outline', [
        //     'height'    => 20,
        //     'width'     => 20,
        //     'color'     => 'var(--sidebar-icon)',
        //     'class'     => 'sidebar-icon'
        // ])->render();
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Text::make(__('Name'), 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make(__('Email'), 'email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:admins,email')
                ->updateRules('unique:admins,email,{{resourceId}}'),

            Password::make(__('Password'), 'password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:6')
                ->updateRules('nullable', 'string', 'min:6'),
        ];
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
}
