<?php

namespace Init\Commerce\Stock\Filament\Resources\StockMovements\Tables;

use BackedEnum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Init\Commerce\Stock\Enums\StockMovementType;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('warehouse.name')->label('Склад'),
                TextColumn::make('catalogItem.sku')->label('SKU')->searchable(),
                TextColumn::make('catalogItem.name')->label('Позиция')->searchable(),
                TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(function (BackedEnum|string|null $state): string {
                        $value = self::stateValue($state);

                        return $value === null
                            ? '—'
                            : (StockMovementType::options()[$value] ?? $value);
                    }),
                TextColumn::make('on_hand_delta')->label('On hand delta'),
                TextColumn::make('allocated_delta')->label('Reserved delta'),
                TextColumn::make('available_after')->label('Available after'),
                TextColumn::make('note')->label('Комментарий')->wrap(),
                TextColumn::make('created_at')->label('Создано')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('warehouse')
                    ->relationship('warehouse', 'name'),
                SelectFilter::make('type')
                    ->options(StockMovementType::options()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private static function stateValue(BackedEnum|string|null $state): ?string
    {
        if ($state instanceof BackedEnum) {
            return (string) $state->value;
        }

        return $state;
    }
}
