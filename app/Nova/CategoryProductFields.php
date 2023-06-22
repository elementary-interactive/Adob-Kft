<?php

namespace App\Nova;

use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\Text;

class CategoryProductFields
{
    /**
     * Get the pivot fields for the relationship.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $relatedModel
     * @return array
     */
    public function __invoke($request, $relatedModel)
    {
        return [
            Hidden::make('order')
                ->default(0),
        ];
    }
}