<?php

namespace App\Nova\Flexible\Layouts;

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Whitecube\NovaFlexibleContent\Layouts\Layout;

class TitleLayout extends Layout
{

    /**
     * The layout's unique identifier
     *
     * @var string
     */
    protected $name = 'title';

    /**
     * The displayed title
     *
     * @var string
     */
    protected $title = 'Címsor';

    /**
     * Get the fields displayed by the layout.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Text::make(__('Title'), 'title')
                ->required()
                ->rules('required', 'string')
                ->help(__('Text of the title')),
            Select::make(__('Design'), 'class')
                ->options([
                    '' => '',
                    'background-white'  => __('White Background'),
                    'background-gray'  => __('Gray Background'),
                ])
        ];
    }
}