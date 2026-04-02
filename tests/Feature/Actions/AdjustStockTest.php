<?php

use Illuminate\Support\Str;
use Init\Commerce\Catalog\Enums\CatalogInventoryMode;
use Init\Commerce\Catalog\Enums\CatalogItemStatus;
use Init\Commerce\Catalog\Enums\CatalogItemType;
use Init\Commerce\Catalog\Models\CatalogItem;
use Init\Commerce\Stock\Actions\AdjustStock;
use Init\Commerce\Stock\Enums\StockMovementType;
use Init\Commerce\Stock\Enums\WarehouseStatus;
use Init\Commerce\Stock\Models\StockLevel;
use Init\Commerce\Stock\Models\StockMovement;
use Init\Commerce\Stock\Models\Warehouse;

function createTrackedCatalogItem(array $attributes = []): CatalogItem
{
    return CatalogItem::query()->create([
        'type' => CatalogItemType::PRODUCT,
        'status' => CatalogItemStatus::ACTIVE,
        'sku' => 'ITEM-'.Str::upper(Str::random(8)),
        'name' => 'Commerce Item',
        'slug' => 'commerce-item-'.Str::lower(Str::random(8)),
        'base_price_amount' => 1000,
        'currency' => 'KZT',
        'inventory_mode' => CatalogInventoryMode::TRACKED,
        ...$attributes,
    ]);
}

function createWarehouse(array $attributes = []): Warehouse
{
    return Warehouse::query()->create([
        'code' => 'MAIN',
        'name' => 'Main Warehouse',
        'status' => WarehouseStatus::ACTIVE,
        'is_default' => true,
        ...$attributes,
    ]);
}

it('records stock movements and updates the current stock level snapshot', function () {
    $item = createTrackedCatalogItem();
    $warehouse = createWarehouse();

    $movement = app(AdjustStock::class)->execute(
        catalogItem: $item,
        warehouse: $warehouse,
        quantityDelta: 5,
        note: 'Initial receipt',
        referenceType: 'seed',
        referenceId: 'stock-bootstrap',
        meta: ['source' => 'test'],
    );

    expect($movement)
        ->toBeInstanceOf(StockMovement::class)
        ->and($movement->type)->toBe(StockMovementType::ADJUSTMENT)
        ->and($movement->on_hand_delta)->toBe(5)
        ->and($movement->allocated_delta)->toBe(0)
        ->and($movement->on_hand_after)->toBe(5)
        ->and($movement->available_after)->toBe(5)
        ->and($movement->reference_type)->toBe('seed')
        ->and($movement->reference_id)->toBe('stock-bootstrap')
        ->and($movement->meta)->toBe(['source' => 'test']);

    $level = StockLevel::query()
        ->where('warehouse_id', $warehouse->getKey())
        ->where('catalog_item_id', $item->getKey())
        ->first();

    expect($level)
        ->not->toBeNull()
        ->and($level->on_hand_quantity)->toBe(5)
        ->and($level->allocated_quantity)->toBe(0)
        ->and($level->available_quantity)->toBe(5);
});

it('rejects stock adjustments for untracked catalog items', function () {
    $item = createTrackedCatalogItem([
        'inventory_mode' => CatalogInventoryMode::UNTRACKED,
    ]);

    $warehouse = createWarehouse([
        'code' => 'SERVICE',
        'name' => 'Service Warehouse',
    ]);

    expect(fn () => app(AdjustStock::class)->execute(
        catalogItem: $item,
        warehouse: $warehouse,
        quantityDelta: 1,
    ))->toThrow(RuntimeException::class, 'Stock operations are allowed only for tracked catalog items.');
});
