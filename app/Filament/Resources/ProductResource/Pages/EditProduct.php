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
            Actions\Action::make('index')
                ->label('Vissza')
                ->color('gray')
                ->icon('heroicon-o-arrow-small-left')
                ->url(fn (): string => route('filament.admin2.resources.products.index')),
            Actions\Action::make('prev')
                ->label(false)
                ->color('gray')
                ->icon('heroicon-o-chevron-double-left')
                ->disabled(fn (Product $record): bool => ($record->prev() == null))
                ->url(function(Product $record): string
                {
                    $url = '';

                    if ($record->prev())
                    {
                        $url = route('filament.admin2.resources.products.edit', ['record' => $record->prev()]);
                    }
                    return $url;
                }),
            Actions\Action::make('next')
                ->label(false)
                ->color('gray')
                ->icon('heroicon-o-chevron-double-right')
                ->disabled(fn (Product $record): bool => ($record->next() == null))
                ->url(function(Product $record): string
                {
                    $url = '';

                    if ($record->next())
                    {
                        $url = route('filament.admin2.resources.products.edit', ['record' => $record->next()]);
                    }
                    return $url;
                }),
            Actions\Action::make('link')
                ->label('Link')
                ->color('gray')
                ->icon('heroicon-o-link')
                ->url(fn (Product $record): string => route('product.show', ['slug' => $record->slug]))
                ->openUrlInNewTab(),
            Actions\Action::make('save')
                ->label('Mentés')
                ->action('save')
                ->successRedirectUrl(fn (): string => route('filament.admin2.resources.products.index')),
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
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
