<?php

namespace Init\Commerce\Stock\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Init\Commerce\Catalog\Models\CatalogItem;

class StockLevel extends Model
{
    use HasUuids;

    protected $table = 'commerce_stock_levels';

    protected $fillable = [
        'warehouse_id',
        'catalog_item_id',
        'on_hand_quantity',
        'allocated_quantity',
        'available_quantity',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'on_hand_quantity' => 'integer',
            'allocated_quantity' => 'integer',
            'available_quantity' => 'integer',
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
