<?php

namespace Init\Commerce\Stock\Filament\Resources\StockLevels\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Init\Commerce\Catalog\Models\CatalogItem;
use Init\Commerce\Stock\Actions\AdjustStock;
use Init\Commerce\Stock\Filament\Resources\StockLevels\StockLevelResource;
use Init\Commerce\Stock\Models\Warehouse;

class ListStockLevels extends ListRecords
{
    protected static string $resource = StockLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('adjustStock')
                ->label('Корректировать остаток')
                ->form([
                    Select::make('catalog_item_id')
                        ->label('Позиция каталога')
                        ->options(CatalogItem::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->required(),
                    Select::make('warehouse_id')
                        ->label('Склад')
                        ->options(Warehouse::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->required(),
                    TextInput::make('quantity_delta')
                        ->label('Изменение количества')
                        ->numeric()
                        ->required(),
                    TextInput::make('note')
                        ->label('Комментарий')
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    app(AdjustStock::class)->execute(
                        catalogItem: CatalogItem::query()->findOrFail($data['catalog_item_id']),
                        warehouse: Warehouse::query()->findOrFail($data['warehouse_id']),
                        quantityDelta: (int) $data['quantity_delta'],
                        note: $data['note'] ?? null,
                    );

                    Notification::make()
                        ->success()
                        ->title('Остаток обновлен')
                        ->send();
                }),
        ];
    }
}
