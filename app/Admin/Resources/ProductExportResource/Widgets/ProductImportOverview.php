<?php

namespace App\Admin\Resources\ProductExportResource\Widgets;

use Filament\Widgets\ChartWidget;

class ProductExportOverview extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            456,123
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
