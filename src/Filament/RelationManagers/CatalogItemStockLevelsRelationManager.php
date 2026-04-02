<?php

namespace Init\Commerce\Stock\Filament\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Init\Commerce\Stock\Actions\AdjustStock;
use Init\Commerce\Stock\Models\Warehouse;

class CatalogItemStockLevelsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockLevels';

    protected static ?string $title = 'Остатки';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('warehouse.name')->label('Склад'),
                TextColumn::make('on_hand_quantity')->label('On hand'),
                TextColumn::make('allocated_quantity')->label('Reserved'),
                TextColumn::make('available_quantity')->label('Available'),
                TextColumn::make('updated_at')->label('Обновлено')->dateTime('d.m.Y H:i'),
            ])
            ->recordActions([
                Action::make('adjust')
                    ->label('Корректировать')
                    ->form([
                        TextInput::make('quantity_delta')
                            ->label('Изменение количества')
                            ->numeric()
                            ->required(),
                        TextInput::make('note')
                            ->label('Комментарий')
                            ->maxLength(255),
                    ])
                    ->action(function ($record, array $data): void {
                        app(AdjustStock::class)->execute(
                            catalogItem: $this->getOwnerRecord(),
                            warehouse: $record->warehouse,
                            quantityDelta: (int) $data['quantity_delta'],
                            note: $data['note'] ?? null,
                        );

                        Notification::make()
                            ->success()
                            ->title('Остаток обновлен')
                            ->send();
                    }),
            ])
            ->headerActions([
                Action::make('adjustForWarehouse')
                    ->label('Корректировать остаток')
                    ->form([
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
                            catalogItem: $this->getOwnerRecord(),
                            warehouse: Warehouse::query()->findOrFail($data['warehouse_id']),
                            quantityDelta: (int) $data['quantity_delta'],
                            note: $data['note'] ?? null,
                        );

                        Notification::make()
                            ->success()
                            ->title('Остаток обновлен')
                            ->send();
                    }),
            ]);
    }
}
