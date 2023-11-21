<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Neon\Models\Statuses\BasicStatus;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('edit')
                ->label('Vissza')
                ->icon('heroicon-o-arrow-small-left')
                ->url(fn (): string => route('filament.admin2.resources.products.index')),
            Actions\ReplicateAction::make()
                ->mutateRecordDataUsing(function (array $data): array {
                    /** Prepend COPY_TAG...
                     */
                    $data['name']        = Product::COPY_TAG . $data['name'];
                    $data['product_id']  = Product::COPY_TAG . $data['product_id'];
                    $data['status']      = BasicStatus::Inactive->value;

                    dd($data);
                    return $data;
                })
                ->successRedirectUrl(fn (Product $replica): string => route('filament.admin2.resources.products.edit', [
                    'record' => $replica,
                ]))
                ->successNotificationTitle('Termék sikeresen duplikálva.'),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
