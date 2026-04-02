<?php

namespace Init\Commerce\Stock\Filament\Resources\StockMovements;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Init\Commerce\Catalog\Filament\Cluster\CommerceCluster;
use Init\Commerce\Stock\Filament\Resources\StockMovements\Pages\ListStockMovements;
use Init\Commerce\Stock\Filament\Resources\StockMovements\Tables\StockMovementsTable;
use Init\Commerce\Stock\Models\StockMovement;

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $cluster = CommerceCluster::class;

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'stock-movements';

    protected static ?string $navigationLabel = 'Движения склада';

    protected static ?string $modelLabel = 'Движение склада';

    protected static ?string $pluralModelLabel = 'Движения склада';

    public static function table(Table $table): Table
    {
        return StockMovementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
        ];
    }
}
