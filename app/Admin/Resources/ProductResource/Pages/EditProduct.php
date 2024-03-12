<?php

namespace App\Admin\Resources\ProductResource\Pages;

use App\Admin\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Livewire\Component;
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
                ->url(function (Product $record, Component $livewire): string {
                    dd($record, $record->isDirty(), $livewire);
                    return route('filament.neon-admin.resources.products.index');
                }),
            Actions\Action::make('prev')
                ->label(false)
                ->color('gray')
                ->icon('heroicon-o-chevron-double-left')
                ->disabled(fn (Product $record): bool => ($record->prev() == null))
                ->url(function (Product $record): string {
                    $url = '';

                    if ($record->prev()) {
                        $url = route('filament.neon-admin.resources.products.edit', ['record' => $record->prev()]);
                    }
                    return $url;
                }),
            Actions\Action::make('next')
                ->label(false)
                ->color('gray')
                ->icon('heroicon-o-chevron-double-right')
                ->disabled(fn (Product $record): bool => ($record->next() == null))
                ->url(function (Product $record): string {
                    $url = '';

                    if ($record->next()) {
                        $url = route('filament.neon-admin.resources.products.edit', ['record' => $record->next()]);
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
                ->successRedirectUrl(fn (): string => route('filament.neon-admin.resources.products.index')),
            Actions\ReplicateAction::make()
                ->beforeReplicaSaved(function (Product $replica): void {
                    $replica->name = $replica->name.Product::COPY_TAG;
                    $replica->slug = $replica->slug.Product::COPY_TAG;
                    $replica->product_id = $replica->product_id.Product::COPY_TAG;
                    $replica->status = BasicStatus::Inactive->value;
                    // Runs after the record has been replicated but before it is saved to the database.
                })
                ->successRedirectUrl(fn (Product $replica): string => route('filament.neon-admin.resources.products.edit', [
                    'record' => $replica,
                ]))
                ->successNotificationTitle('Termék sikeresen duplikálva.'),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    /**
     * @param $propertyName
     */
    public function updated($propertyName): void
    {
        $data = $this->validateOnly($propertyName);
        // $data = $data['data'];
        // dd('it worked, do what you need to do.', $propertyName, $data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
