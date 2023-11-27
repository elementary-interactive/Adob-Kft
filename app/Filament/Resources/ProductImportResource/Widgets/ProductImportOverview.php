<?php

namespace App\Filament\Resources\ProductImportResource\Widgets;

use Filament\Widgets\ChartWidget;

class ProductImportOverview extends ChartWidget
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
