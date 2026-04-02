<?php

namespace Init\Commerce\Stock\Filament\Resources\Warehouses\Tables;

use BackedEnum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Init\Commerce\Stock\Enums\WarehouseStatus;

class WarehousesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Код')->sortable()->searchable(),
                TextColumn::make('name')->label('Название')->sortable()->searchable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->badge()
                    ->formatStateUsing(function (BackedEnum|string|null $state): string {
                        $value = self::stateValue($state);

                        return $value === null
                            ? '—'
                            : (WarehouseStatus::options()[$value] ?? $value);
                    }),
                IconColumn::make('is_default')->label('Default')->boolean(),
                TextColumn::make('responsible_party_id')->label('Ответственная party')->toggleable(),
                TextColumn::make('updated_at')->label('Обновлено')->dateTime('d.m.Y H:i'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(WarehouseStatus::options()),
            ]);
    }

    private static function stateValue(BackedEnum|string|null $state): ?string
    {
        if ($state instanceof BackedEnum) {
            return (string) $state->value;
        }

        return $state;
    }
}
