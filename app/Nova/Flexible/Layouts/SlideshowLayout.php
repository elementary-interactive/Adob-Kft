<?php

namespace App\Nova\Flexible\Layouts;

use App\Models\Slideshow;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Whitecube\NovaFlexibleContent\Layouts\Layout;
use Neon\Models\Scopes\ActiveScope;
use Neon\Models\Scopes\PublishedScope;
use Neon\Site\Models\Scopes\SiteScope;

class SlideshowLayout extends Layout
{

    /**
     * The layout's unique identifier
     *
     * @var string
     */
    protected $name = 'slideshow';

    /**
     * The displayed title
     *
     * @var string
     */
    protected $title = 'Slideshow';

    /**
     * Get the fields displayed by the layout.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Select::make(__('Slideshow'), 'slideshow')->options(
                Slideshow::withoutGlobalScopes([
                    PublishedScope::class,
                    ActiveScope::class,
                    SiteScope::class
                ])
                    ->get(['id', 'name'])
                    ->pluck('name', 'id')
            )
                ->help(__('Select previously stored slideshow to show.'))
            
        ];
    }
}