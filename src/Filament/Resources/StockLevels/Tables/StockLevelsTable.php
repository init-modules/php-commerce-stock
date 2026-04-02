<?php

namespace Init\Commerce\Stock\Filament\Resources\StockLevels\Tables;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Init\Commerce\Stock\Actions\AdjustStock;

class StockLevelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('catalogItem.sku')->label('SKU')->searchable(),
                TextColumn::make('catalogItem.name')->label('Позиция')->searchable(),
                TextColumn::make('warehouse.name')->label('Склад')->searchable(),
                TextColumn::make('on_hand_quantity')->label('On hand')->sortable(),
                TextColumn::make('allocated_quantity')->label('Reserved')->sortable(),
                TextColumn::make('available_quantity')->label('Available')->sortable(),
                TextColumn::make('updated_at')->label('Обновлено')->dateTime('d.m.Y H:i'),
            ])
            ->filters([
                SelectFilter::make('warehouse')
                    ->relationship('warehouse', 'name'),
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
                            catalogItem: $record->catalogItem,
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
            ->defaultSort('updated_at', 'desc');
    }
}
