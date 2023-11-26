<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('ADOB_batch_import')
            ->label('Importálás')
            ->form([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                // ...
            ]),
            // ->using(function (array $data, string $model): ProductImport {
            //     // return $model::create($data);
            // }),
        ];
    }
}
