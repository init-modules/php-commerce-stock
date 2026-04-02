<?php

namespace Init\Commerce\Stock\Filament\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Init\Commerce\Stock\Filament\Resources\StockMovements\Tables\StockMovementsTable;

class CatalogItemStockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    protected static ?string $title = 'Движения склада';

    public function table(Table $table): Table
    {
        return StockMovementsTable::configure($table);
    }
}
