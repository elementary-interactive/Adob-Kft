<?php

namespace App\Admin\Resources\ProductImportResource\Pages;

use App\Admin\Resources\ProductImportResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductImport extends CreateRecord
{
    protected static string $resource = ProductImportResource::class;
}
