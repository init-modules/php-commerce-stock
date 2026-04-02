<?php

namespace Init\Commerce\Stock\Filament\Resources\Warehouses;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Init\Commerce\Catalog\Filament\Cluster\CommerceCluster;
use Init\Commerce\Stock\Filament\Resources\Warehouses\Pages\CreateWarehouse;
use Init\Commerce\Stock\Filament\Resources\Warehouses\Pages\EditWarehouse;
use Init\Commerce\Stock\Filament\Resources\Warehouses\Pages\ListWarehouses;
use Init\Commerce\Stock\Filament\Resources\Warehouses\Schemas\WarehouseForm;
use Init\Commerce\Stock\Filament\Resources\Warehouses\Tables\WarehousesTable;
use Init\Commerce\Stock\Models\Warehouse;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?string $cluster = CommerceCluster::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'warehouses';

    protected static ?string $navigationLabel = 'Склады';

    protected static ?string $modelLabel = 'Склад';

    protected static ?string $pluralModelLabel = 'Склады';

    public static function form(Schema $schema): Schema
    {
        return WarehouseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarehousesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWarehouses::route('/'),
            'create' => CreateWarehouse::route('/create'),
            'edit' => EditWarehouse::route('/{record}/edit'),
        ];
    }
}
