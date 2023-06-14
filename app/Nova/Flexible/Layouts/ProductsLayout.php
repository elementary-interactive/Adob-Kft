<?php

namespace App\Nova\Flexible\Layouts;

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Whitecube\NovaFlexibleContent\Layouts\Layout;

class ProductsLayout extends Layout
{

    /**
     * The layout's unique identifier
     *
     * @var string
     */
    protected $name = 'products';

    /**
     * The displayed title
     *
     * @var string
     */
    protected $title = 'Termékek listája';

    /**
     * Get the fields displayed by the layout.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Number::make(__('Limit'))
                ->min(1)
                ->max(50)
                ->step(1)
                ->help(__('How many products should shown on the page?')),
            Select::make(__('Category'))->options([
                'list' => __('List view')
            ])
                ->nullable()
                ->help(__('Show products only from this category...'))
            
        ];
    }
}