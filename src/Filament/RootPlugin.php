<?php

namespace Init\Commerce\Stock\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;

class RootPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'init-commerce-stock';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(
            in: __DIR__ . '/Resources',
            for: 'Init\\Commerce\\Stock\\Filament\\Resources',
        );
    }

    public function boot(Panel $panel): void
    {
    }
}
