<?php

namespace App\Admin\Resources\ProductImportResource\Pages;

use App\Admin\Resources\ProductImportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductImport extends ViewRecord
{
    protected static string $resource = ProductImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('index')
                ->label('Vissza')
                ->color('gray')
                ->icon('heroicon-o-arrow-small-left')
                ->url(fn (): string => route('filament.admin.resources.product-imports.index')),
        ];
    }
}
