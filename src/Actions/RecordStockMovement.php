<?php

namespace Init\Commerce\Stock\Actions;

use Illuminate\Support\Facades\DB;
use Init\Commerce\Catalog\Models\CatalogItem;
use Init\Commerce\Stock\Enums\StockMovementType;
use Init\Commerce\Stock\Models\StockLevel;
use Init\Commerce\Stock\Models\StockMovement;
use Init\Commerce\Stock\Models\Warehouse;

class RecordStockMovement
{
    /**
     * @param array<string, mixed> $meta
     */
    public function execute(
        CatalogItem $catalogItem,
        Warehouse $warehouse,
        StockMovementType $type,
        int $onHandDelta = 0,
        int $allocatedDelta = 0,
        ?string $note = null,
        ?string $referenceType = null,
        ?string $referenceId = null,
        array $meta = [],
    ): StockMovement {
        if (! $catalogItem->isTracked()) {
            throw new \RuntimeException('Stock operations are allowed only for tracked catalog items.');
        }

        return DB::transaction(function () use (
            $catalogItem,
            $warehouse,
            $type,
            $onHandDelta,
            $allocatedDelta,
            $note,
            $referenceType,
            $referenceId,
            $meta,
        ): StockMovement {
            $level = StockLevel::query()
                ->where('warehouse_id', $warehouse->getKey())
                ->where('catalog_item_id', $catalogItem->getKey())
                ->lockForUpdate()
                ->first();

            if (! $level instanceof StockLevel) {
                $level = new StockLevel([
                    'warehouse_id' => $warehouse->getKey(),
                    'catalog_item_id' => $catalogItem->getKey(),
                    'on_hand_quantity' => 0,
                    'allocated_quantity' => 0,
                    'available_quantity' => 0,
                ]);
            }

            $onHandAfter = (int) $level->on_hand_quantity + $onHandDelta;
            $allocatedAfter = (int) $level->allocated_quantity + $allocatedDelta;
            $availableAfter = $onHandAfter - $allocatedAfter;

            if ($onHandAfter < 0 || $allocatedAfter < 0 || $availableAfter < 0) {
                throw new \RuntimeException('Stock movement would produce a negative balance.');
            }

            $level->forceFill([
                'on_hand_quantity' => $onHandAfter,
                'allocated_quantity' => $allocatedAfter,
                'available_quantity' => $availableAfter,
            ])->save();

            return StockMovement::query()->create([
                'warehouse_id' => $warehouse->getKey(),
                'catalog_item_id' => $catalogItem->getKey(),
                'type' => $type,
                'on_hand_delta' => $onHandDelta,
                'allocated_delta' => $allocatedDelta,
                'on_hand_after' => $onHandAfter,
                'allocated_after' => $allocatedAfter,
                'available_after' => $availableAfter,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'note' => $note,
                'meta' => $meta,
            ]);
        });
    }
}
