<?php

namespace App\Nova;

use Neon\Models\Scopes\ActiveScope;
use Neon\Models\Scopes\PublishedScope;
use Neon\Site\Models\Scopes\SiteScope;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

use Whitecube\NovaFlexibleContent\Flexible;

use Eminiarts\Tabs\Traits\HasTabs;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\Tab;
/** Nova fields.
 * 
 */
use Laravel\Nova\Fields\{
    BelongsToMany,
    Boolean,
    DateTime,
    HasMany,
    Heading,
    Image,
    KeyValue,
    MorphMany,
    Select,
    Slug,
    Text,
    Textarea,
};

class Link extends Resource
{
    use HasTabs;

    // use Orderable;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Neon\Models\Link::class;

    /** Hide from navigation.
     *
     * @var boolean
     */
    // public static $displayInNavigation = false;

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
    
    /** Show as many items as it could be.
     * 
     * @var integer
     */
    public static $perPageViaRelationship = 15;

    public static function label()
    {
        return __('Links');
    }

    public static function singularLabel()
    {
        return __('Link');
    }

    // public static function icon() 
    // {
    //     return view('nova::icon.svg-link', [
    //         'height'    => 20,
    //         'width'     => 20,
    //         'color'     => 'var(--sidebar-icon)',
    //         'class'     => 'sidebar-icon'
    //     ])->render();
    // }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $model = $this;

        $layouts = config('neon.content.layouts', []);
        $flexible = Flexible::make(__('Content'), 'content');

        $fields = [
            BelongsToMany::make(__('Site'), 'site', \App\Nova\Site::class)
                ->fields(function ($request, $relatedModel) {
                    return [
                        Text::make(__('Kapcsolat típusa'), 'dependence_type')
                            ->default(\Neon\Models\Link::class)
                            ->readonly()
                            ->hideFromIndex(),
                    ];
                }),
            HasMany::make(__('Menu'), 'menus', \App\Nova\MenuItem::class)
                ->nullable(),
            Text::make(__('Title'), 'title')
                // ->slug('slug')
                ->rules('required', 'max:255'),
            Slug::make('URI', 'slug')
                // ->slugifyOptions([
                //     'lang'  => 'hu'
                // ])
                ->from('title')
                ->hideFromIndex()
                ->hideFromDetail(),
            Text::make('', function() use ($model) {
                return "<a style=\"color: inherit;\" href=\"".url($model->href)."\" target=\"_blank\" title=\"{$model->href}\">".view('nova::icon.svg-link', [
                    'color'     => 'rgb(var(--colors-gray-400), 0.75)'
                ])->render()."</a>";
            })
                ->asHtml()
                ->hideFromDetail(),
            Heading::make(__('Sharing')),
            Text::make(__('Sharing Title'), 'og_title')
                ->help(__('Sharing title will shown on social netwok sharing box.'))
                ->hideFromIndex(),
            Textarea::make(__('Description'), 'og_description')
                ->hideFromIndex(),
            Image::make(__('Image'), 'og_image')
                ->store(function(Request $request) {
                    $request->file('og_image')->storeAs('links', $request->file('og_image')->getFilename().'_'.$request->file('og_image')->getClientOriginalName(), config('nova.storage_disk'));
                    // dd($file);

                    return [
                        'og_image' => 'links/'.$request->file('og_image')->getFilename().'_'.$request->file('og_image')->getClientOriginalName(),
                    ];
                })
                ->hideFromIndex(),
            Heading::make(__('Availability')),
            Boolean::make(__('Available'), 'status')
                ->trueValue(\Neon\Models\Statuses\BasicStatus::Active->value)
                ->falseValue(\Neon\Models\Statuses\BasicStatus::Inactive->value)
                ->help(__('Check this on if you want to link be available!')),
            DateTime::make(__('Published at'), 'published_at')
                ->help('If the link is active, will be shown at this time and date.'),
            DateTime::make(__('Expire at'), 'expired_at')
                ->help('Not mandatory. If it\'s empty, availability never expires, if not, the link will accessible until this time.'),
            HasMany::make('Elemek', 'children', \App\Nova\Link::class),
            // MorphOne::make('Tartalom', 'content', \App\Nova\Content::class)
        ];

        $advanced_fields = [
            Heading::make('Haladó beállítások')
                ->hideFromDetail(),
            Text::make('URI', 'url')
                ->hideFromIndex()
                ->help('Automatikusan generált URI a menüstruktúra alapján.'),
            Select::make('Kérés', 'method')
                ->options([
                    'GET' => 'GET',
                    'POST' => 'POST',
                    'PUT' => 'PUT',
                    'PATCH' => 'PATCH',
                    'DELETE' => 'DELETE'
                ])
                ->hideFromIndex()
                ->hideFromDetail(),
            Text::make('Útvonal', 'route')
                ->help('A keretrendszerben előre definiált útvonal. Bővebb információ: <a href="https://laravel.com/docs/6.x/routing" target="_blank">https://laravel.com/docs/6.x/routing</a>')
                ->hideFromIndex()
                ->hideFromDetail(),
            KeyValue::make('Paraméterek', 'parameters')
                ->rules('json')
                ->hideFromDetail(),
            Text::make('Külső link', 'link')
                ->help('Külső hivatkozás, például: https://brightly.hu')
                ->hideFromIndex()
                ->hideFromDetail(),
            MorphMany::make(__('Variables'), 'attributeValues', AttributeValue::class)
        ];

        // $advanced_fields = \Neon\Attributable\Models\Attribute::where('class', get_class($model->resource))->get();
        // if ($advanced_fields->count())
        // {
        //     $fields[] = Heading::make(__('Advanced settings'));
        //     foreach ($advanced_fields as $field)
        //     {
        //         $field_class = '\\Laravel\\Nova\\Fields\\'.$field->field;
        //         $fields[] = $field_class::make($field->name, $field->slug)
        //             ->rules($field->rules)
        //             ->hideFromIndex();
        //     }
        // }

        foreach ($layouts as $layout)
        {
            $flexible->addLayout($layout);
        }

        // $fields[] = $flexible;


        $tabs = Tabs::make(__('Link'), [
            Tab::make(__('Page'), $fields),
            Tab::make(__('Content'), [$flexible]),
            Tab::make(__('Advanced Settings'), $advanced_fields),
        ]);

        return [$tabs];
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
            'Neon\Models\Scopes\ActiveScope',
            'Neon\Models\Scopes\PublishedScope',
            'Neon\Site\Models\Scopes\SiteScope',
        ]);

        /** Empty orders and the order value... */
        $next->getQuery()->orders = [];
        $next->orderBy(
            'title',
            'asc'
        );

        return $next;
    }
}
