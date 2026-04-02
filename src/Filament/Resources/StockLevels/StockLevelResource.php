<?php

namespace Init\Commerce\Stock\Filament\Resources\StockLevels;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Init\Commerce\Catalog\Filament\Cluster\CommerceCluster;
use Init\Commerce\Stock\Filament\Resources\StockLevels\Pages\ListStockLevels;
use Init\Commerce\Stock\Filament\Resources\StockLevels\Tables\StockLevelsTable;
use Init\Commerce\Stock\Models\StockLevel;

class StockLevelResource extends Resource
{
    protected static ?string $model = StockLevel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?string $cluster = CommerceCluster::class;

    protected static ?string $slug = 'stock-levels';

    protected static ?string $navigationLabel = 'Остатки';

    protected static ?string $modelLabel = 'Остаток';

    protected static ?string $pluralModelLabel = 'Остатки';

    public static function table(Table $table): Table
    {
        return StockLevelsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockLevels::route('/'),
        ];
    }
}
