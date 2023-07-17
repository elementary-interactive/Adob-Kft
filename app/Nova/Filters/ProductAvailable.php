<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\BooleanFilter;
use Laravel\Nova\Http\Requests\NovaRequest;
use Neon\Models\Statuses\BasicStatus;

class ProductAvailable extends BooleanFilter
{
    /**
     * The displayable name of the filter.
     *
     * @var string
     */
    public $name = "Termék elérhetőség";
    /**
     * Apply the filter to the given query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(NovaRequest $request, $query, $value)
    {
        if ($value[BasicStatus::Active->value]) {
            return $query->where('status', BasicStatus::Active->value);
        }
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function options(NovaRequest $request)
    {
        return [
            __('Available') => BasicStatus::Active->value
        ];
    }
}
