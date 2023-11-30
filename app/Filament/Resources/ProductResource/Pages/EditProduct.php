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
                ->beforeReplicaSaved(function (Product $replica): void {
                    $replica->name = Product::COPY_TAG . $replica->name;
                    $replica->slug = Product::COPY_TAG . $replica->slug;
                    $replica->product_id = Product::COPY_TAG . $replica->product_id;
                    $replica->status = BasicStatus::Inactive->value;
                    // Runs after the record has been replicated but before it is saved to the database.
                })
                ->successRedirectUrl(fn (Product $replica): string => route('filament.admin2.resources.products.edit', [
                    'record' => $replica,
                ]))
                ->successNotificationTitle('Termék sikeresen duplikálva.'),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\Action::make('save')
                ->label('Mentés')
                ->action(fn () => $this->record->save())
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
