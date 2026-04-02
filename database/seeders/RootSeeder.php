<?php

namespace Init\Commerce\Stock\Database\Seeders;

use Init\Core\Database\PackageSeeder;

class RootSeeder extends PackageSeeder
{
    public static function dependencies(): array
    {
        return [
            \Init\Commerce\Catalog\Database\Seeders\RootSeeder::class,
        ];
    }

    public function run(): void
    {
        if (app()->isProduction() || ! config('commerce_stock.seed_demo_data', true)) {
            return;
        }

        $this->call([
            DemoWarehouseSeeder::class,
            DemoStockSeeder::class,
        ]);
    }
}
