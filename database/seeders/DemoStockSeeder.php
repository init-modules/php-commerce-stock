<?php

namespace Init\Commerce\Stock\Database\Seeders;

use Illuminate\Database\Seeder;
use Init\Commerce\Catalog\Enums\CatalogInventoryMode;
use Init\Commerce\Catalog\Models\CatalogItem;
use Init\Commerce\Stock\Actions\AdjustStock;
use Init\Commerce\Stock\Models\Warehouse;

class DemoStockSeeder extends Seeder
{
    public function run(): void
    {
        $warehouse = Warehouse::query()->default()->first();

        if (! $warehouse instanceof Warehouse) {
            return;
        }

        $items = CatalogItem::query()
            ->where('inventory_mode', CatalogInventoryMode::TRACKED->value)
            ->get();

        foreach ($items as $index => $item) {
            if ($item->stockLevels()->where('warehouse_id', $warehouse->getKey())->exists()) {
                continue;
            }

            app(AdjustStock::class)->execute(
                catalogItem: $item,
                warehouse: $warehouse,
                quantityDelta: 20 + ($index * 5),
                note: 'Seed opening balance',
                meta: ['seeded' => true],
            );
        }
    }
}
