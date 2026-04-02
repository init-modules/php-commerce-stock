<?php

namespace Init\Commerce\Stock\Actions;

use InvalidArgumentException;
use Init\Commerce\Catalog\Models\CatalogItem;
use Init\Commerce\Stock\Enums\StockMovementType;
use Init\Commerce\Stock\Models\StockMovement;
use Init\Commerce\Stock\Models\Warehouse;

class ReleaseStock
{
    /**
     * @param array<string, mixed> $meta
     */
    public function execute(
        CatalogItem|string $catalogItem,
        int $quantity,
        Warehouse|string|null $warehouse = null,
        ?string $referenceType = null,
        ?string $referenceId = null,
        array $meta = [],
    ): StockMovement {
        $catalogItem = $catalogItem instanceof CatalogItem ? $catalogItem : CatalogItem::query()->findOrFail($catalogItem);

        if ($quantity <= 0) {
            throw new InvalidArgumentException('Released quantity must be positive.');
        }

        $warehouse = $warehouse instanceof Warehouse ? $warehouse : Warehouse::query()
            ->when($warehouse, fn ($query) => $query->whereKey($warehouse), fn ($query) => $query->where('is_default', true))
            ->first();

        if (! $warehouse instanceof Warehouse) {
            throw new \RuntimeException('Default warehouse not configured.');
        }

        return app(RecordStockMovement::class)->execute(
            catalogItem: $catalogItem,
            warehouse: $warehouse,
            type: StockMovementType::RELEASE,
            onHandDelta: 0,
            allocatedDelta: -$quantity,
            note: 'Released allocation',
            referenceType: $referenceType,
            referenceId: $referenceId,
            meta: $meta,
        );
    }
}
