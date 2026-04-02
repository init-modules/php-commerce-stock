<?php

namespace Init\Commerce\Stock\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Init\Commerce\Stock\Enums\WarehouseStatus;
use Init\Parties\Models\Party;

class Warehouse extends Model
{
    use HasUuids;

    protected $table = 'commerce_warehouses';

    protected $fillable = [
        'code',
        'name',
        'status',
        'is_default',
        'responsible_party_id',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => WarehouseStatus::class,
            'is_default' => 'boolean',
            'meta' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Warehouse $warehouse): void {
            $warehouse->code = Str::upper((string) $warehouse->code);

            if ($warehouse->is_default) {
                static::query()
                    ->whereKeyNot($warehouse->getKey())
                    ->update(['is_default' => false]);
            }
        });
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    public function responsibleParty(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'responsible_party_id');
    }

    public function stockLevels(): HasMany
    {
        return $this->hasMany(StockLevel::class, 'warehouse_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'warehouse_id');
    }
}
