<?php

namespace Init\Commerce\Stock\Enums;

enum StockMovementType: string
{
    case ADJUSTMENT = 'adjustment';
    case ALLOCATION = 'allocation';
    case RELEASE = 'release';

    public static function options(): array
    {
        return [
            self::ADJUSTMENT->value => 'Корректировка',
            self::ALLOCATION->value => 'Резерв',
            self::RELEASE->value => 'Снятие резерва',
        ];
    }
}
