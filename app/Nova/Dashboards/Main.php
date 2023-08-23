<?php

namespace App\Nova\Dashboards;

// use App\Nova\Metrics\BrandsPerDay;
use App\Nova\Metrics\Products;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new Products,
            // new BrandsPerDay
        ];
    }
}
