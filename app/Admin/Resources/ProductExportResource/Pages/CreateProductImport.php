<?php

namespace App\Admin\Resources\ProductExportResource\Pages;

use App\Admin\Resources\ProductExportResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductImport extends CreateRecord
{
    protected static string $resource = ProductExportResource::class;
}
