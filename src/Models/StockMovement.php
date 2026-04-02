<?php

namespace Init\Commerce\Stock\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Init\Commerce\Catalog\Models\CatalogItem;
use Init\Commerce\Stock\Enums\StockMovementType;

class StockMovement extends Model
{
    use HasUuids;

    protected $table = 'commerce_stock_movements';

    protected $fillable = [
        'warehouse_id',
        'catalog_item_id',
        'type',
        'on_hand_delta',
        'allocated_delta',
        'on_hand_after',
        'allocated_after',
        'available_after',
        'reference_type',
        'reference_id',
        'note',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'type' => StockMovementType::class,
            'on_hand_delta' => 'integer',
            'allocated_delta' => 'integer',
            'on_hand_after' => 'integer',
            'allocated_after' => 'integer',
            'available_after' => 'integer',
            'meta' => 'array',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class, 'catalog_item_id');
    }
}
