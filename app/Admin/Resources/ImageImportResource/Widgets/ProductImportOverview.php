<?php

namespace App\Admin\Resources\ImageImportResource\Widgets;

use Filament\Widgets\ChartWidget;

class ImageImportOverview extends ChartWidget
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
