<?php

namespace Init\Commerce\Stock\Actions;

use InvalidArgumentException;
use Init\Commerce\Catalog\Models\CatalogItem;
use Init\Commerce\Stock\Enums\StockMovementType;
use Init\Commerce\Stock\Models\StockMovement;
use Init\Commerce\Stock\Models\Warehouse;

class AdjustStock
{
    /**
     * @param array<string, mixed> $meta
     */
    public function execute(
        CatalogItem|string $catalogItem,
        Warehouse|string $warehouse,
        int $quantityDelta,
        ?string $note = null,
        ?string $referenceType = null,
        ?string $referenceId = null,
        array $meta = [],
    ): StockMovement {
        $catalogItem = $catalogItem instanceof CatalogItem ? $catalogItem : CatalogItem::query()->findOrFail($catalogItem);
        $warehouse = $warehouse instanceof Warehouse ? $warehouse : Warehouse::query()->findOrFail($warehouse);

        if ($quantityDelta === 0) {
            throw new InvalidArgumentException('Stock adjustment delta cannot be zero.');
        }

        return app(RecordStockMovement::class)->execute(
            catalogItem: $catalogItem,
            warehouse: $warehouse,
            type: StockMovementType::ADJUSTMENT,
            onHandDelta: $quantityDelta,
            allocatedDelta: 0,
            note: $note,
            referenceType: $referenceType,
            referenceId: $referenceId,
            meta: $meta,
        );
    }
}
