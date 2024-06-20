<?php

namespace App\Admin\Resources\ImageImportResource\Pages;

use App\Admin\Resources\ImageImportResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateImageImport extends CreateRecord
{
    protected static string $resource = ImageImportResource::class;
}
