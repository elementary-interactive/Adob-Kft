<?php

namespace App\Nova\Flexible\Layouts;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Whitecube\NovaFlexibleContent\Layouts\Layout;
use Illuminate\Http\Request;

class NewsLayout extends Layout
{
    /**
     * The layout's unique identifier
     *
     * @var string
     */
    protected $name = 'news';

    /**
     * The maximum amount of this layout type that can be added
     */
    protected $limit = 1;

    /**
     * The displayed title
     *
     * @var string
     */
    protected $title = 'Friss hírek';

    /**
     * Get the fields displayed by the layout.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Number::make(__('Limit'), 'limit')
                ->min(1)
                ->max(50)
                ->step(1)
                ->help(__('How many news should shown on the page?')),
            Select::make(__('Template'), 'template')->options([
                'list'  => __('List view'),
                'block' => __('Block view')
            ]),
            Select::make(__('Design'), 'class')->options([
                '' => '',
                'background-white'  => __('White Background'),
                'background-gray'  => __('Gray Background'),
            ]),
            Heading::make(__('Shortcut')),
            Boolean::make(__('Create Shortcut'), 'shortcut_need')
                ->help(__("If you want to put a navigation bar somewhere on the page, and if you would like to include this there, you should mark here yes.")),
            Image::make(__('Shortcut Image'), 'shortcut_image')
                ->store(function(Request $request) {
                    $request->file('shortcut_image')->storeAs('shortcuts', $request->file('shortcut_image')->getFilename().'_'.$request->file('shortcut_image')->getClientOriginalName(), config('nova.storage_disk'));
                    // dd($file);

                    return [
                        'shortcut_image' => asset('storage/shortcuts/'.$request->file('shortcut_image')->getFilename().'_'.$request->file('shortcut_image')->getClientOriginalName()),
                    ];
                }),
        ];
    }
}