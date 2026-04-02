<?php

namespace Init\Commerce\Stock\Enums;

enum WarehouseStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ARCHIVED = 'archived';

    public static function options(): array
    {
        return [
            self::ACTIVE->value => 'Активен',
            self::INACTIVE->value => 'Неактивен',
            self::ARCHIVED->value => 'Архив',
        ];
    }
}
