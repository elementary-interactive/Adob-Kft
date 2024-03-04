<?php

namespace App\Admin\Resources\ProductImportResource\Pages;

use App\Admin\Resources\ProductImportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductImport extends EditRecord
{
    protected static string $resource = ProductImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
