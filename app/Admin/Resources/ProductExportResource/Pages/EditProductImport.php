<?php

namespace App\Admin\Resources\ProductExportResource\Pages;

use App\Admin\Resources\ProductExportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductExport extends EditRecord
{
    protected static string $resource = ProductExportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
