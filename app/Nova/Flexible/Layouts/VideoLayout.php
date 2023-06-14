<?php

namespace App\Nova\Flexible\Layouts;

use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Image;
use Whitecube\NovaFlexibleContent\Layouts\Layout;
use Illuminate\Http\Request;

class VideoLayout extends Layout
{

    /**
     * The layout's unique identifier
     *
     * @var string
     */
    protected $name = 'video';

    /**
     * The displayed title
     *
     * @var string
     */
    protected $title = 'Videó';

    /**
     * Get the fields displayed by the layout.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Text::make(__('Title'), 'title')
                ->help(__('Title above the videó')),
            Code::make(__('Embed code'), 'code')
                ->language('htmlmixed')
                ->help(__('Code to embed video')),
            // Select::make(__('Design'), 'class')
            //     ->options([
            //         'background-white'  => __('White Background'),
            //         'background-gray'  => __('Gray Background'),
            //     ])
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