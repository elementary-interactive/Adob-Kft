<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImport;
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
            ->modalHeading('Importálás')
            ->modalDescription('ADOB Excel file importálása.')
            ->modalSubmitActionLabel('Importálás')
            ->icon('heroicon-o-arrow-up-tray')
            ->modalIcon('heroicon-o-arrow-up-tray')
            ->form([
                Forms\Components\Toggle::make('header')
                    ->label('Fejléc?')
                    ->required(),
                Forms\Components\FileUpload::make('file')
                    ->label('Excel fájl')
                    ->required()
                    ->preserveFilenames()
            ])
            ->action(function (array $data, array $arguments): void {
                dd(func_get_args());
            }),
            // ->slideOver(),
        ];
    }
}
