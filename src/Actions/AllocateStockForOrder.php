<?php

namespace Init\Commerce\Stock\Actions;

use Init\Commerce\Catalog\Models\CatalogItem;
use Init\Commerce\Order\Models\Order;
use Init\Commerce\Stock\Enums\StockMovementType;
use Init\Commerce\Stock\Models\StockMovement;
use Init\Commerce\Stock\Models\Warehouse;
use InvalidArgumentException;

class AllocateStockForOrder
{
    /**
     * @param  array<string, mixed>  $meta
     * @return array<int, StockMovement>|StockMovement
     */
    public function execute(
        mixed $subject,
        ?int $quantity = null,
        Warehouse|string|null $warehouse = null,
        ?string $referenceType = null,
        ?string $referenceId = null,
        array $meta = [],
    ): array|StockMovement {
        if (class_exists(Order::class)
            && $subject instanceof Order
        ) {
            return $this->executeForOrder($subject, $warehouse, $meta);
        }

        $catalogItem = $subject instanceof CatalogItem ? $subject : CatalogItem::query()->findOrFail((string) $subject);

        if ($quantity === null || $quantity <= 0) {
            throw new InvalidArgumentException('Allocated quantity must be positive.');
        }

        $warehouse = $this->resolveWarehouse($warehouse);

        return app(RecordStockMovement::class)->execute(
            catalogItem: $catalogItem,
            warehouse: $warehouse,
            type: StockMovementType::ALLOCATION,
            onHandDelta: 0,
            allocatedDelta: $quantity,
            note: 'Allocated for order',
            referenceType: $referenceType,
            referenceId: $referenceId,
            meta: $meta,
        );
    }

    /**
     * @param  Order  $order
     * @param  array<string, mixed>  $meta
     * @return array<int, StockMovement>
     */
    protected function executeForOrder(
        object $order,
        Warehouse|string|null $warehouse = null,
        array $meta = [],
    ): array {
        $warehouse = $this->resolveWarehouse($warehouse);
        $order->loadMissing('items');

        $movements = [];

        foreach ($order->items as $item) {
            if (! (bool) data_get($item->catalog_snapshot, 'tracked', false)) {
                continue;
            }

            $catalogItemType = $item->catalog_item_type;

            if (! is_string($catalogItemType) || ! class_exists($catalogItemType)) {
                throw new \RuntimeException('Tracked order item references an unavailable catalog item type.');
            }

            $catalogItem = $catalogItemType::query()->find($item->catalog_item_id);

            if (! $catalogItem instanceof CatalogItem) {
                throw new \RuntimeException('Tracked order item references a missing catalog item.');
            }

            $movements[] = app(RecordStockMovement::class)->execute(
                catalogItem: $catalogItem,
                warehouse: $warehouse,
                type: StockMovementType::ALLOCATION,
                onHandDelta: 0,
                allocatedDelta: (int) $item->quantity,
                note: 'Allocated for order',
                referenceType: $order->getMorphClass(),
                referenceId: (string) $order->getKey(),
                meta: [
                    ...$meta,
                    'order_item_id' => (string) $item->getKey(),
                    'order_number' => $order->number,
                ],
            );
        }

        return $movements;
    }

    protected function resolveWarehouse(Warehouse|string|null $warehouse = null): Warehouse
    {
        $resolvedWarehouse = $warehouse instanceof Warehouse ? $warehouse : Warehouse::query()
            ->when($warehouse, fn ($query) => $query->whereKey($warehouse), fn ($query) => $query->where('is_default', true))
            ->first();

        if (! $resolvedWarehouse instanceof Warehouse) {
            throw new \RuntimeException('Default warehouse not configured.');
        }

        return $resolvedWarehouse;
    }
}
