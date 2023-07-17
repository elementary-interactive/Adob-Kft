<?php

namespace App\Nova\Flexible\Layouts;

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Whitecube\NovaFlexibleContent\Layouts\Layout;

class BrowserLayout extends Layout
{

    /**
     * The layout's unique identifier
     *
     * @var string
     */
    protected $name = 'browser';

    /**
     * The displayed title
     *
     * @var string
     */
    protected $title = 'Kategóriák és termékek listája';

    /**
     * Get the fields displayed by the layout.
     *
     * @return array
     */
    public function fields()
    {
        return [
           
        ];
    }
}