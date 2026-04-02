<?php

namespace Init\Commerce\Stock\Database\Seeders;

use Illuminate\Database\Seeder;
use Init\Commerce\Stock\Enums\WarehouseStatus;
use Init\Commerce\Stock\Models\Warehouse;

class DemoWarehouseSeeder extends Seeder
{
    public function run(): void
    {
        Warehouse::query()->updateOrCreate(
            ['code' => (string) config('commerce_stock.default_warehouse_code', 'MAIN')],
            [
                'name' => 'Main Warehouse',
                'status' => WarehouseStatus::ACTIVE,
                'is_default' => true,
            ],
        );
    }
}
