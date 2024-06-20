<?php

namespace App\Admin\Resources\ImageImportResource\Pages;

use App\Admin\Resources\ImageImportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImageImport extends EditRecord
{
    protected static string $resource = ImageImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
