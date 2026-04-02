<?php

namespace Init\Commerce\Stock\Filament\Resources\StockMovements\Pages;

use Filament\Resources\Pages\ListRecords;
use Init\Commerce\Stock\Filament\Resources\StockMovements\StockMovementResource;

class ListStockMovements extends ListRecords
{
    protected static string $resource = StockMovementResource::class;
}
