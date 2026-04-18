<?php

namespace Init\Commerce\Stock;

use Illuminate\Support\Facades\Route;
use Init\Commerce\Catalog\Ext\CatalogItemFilamentExtRegistry;
use Init\Commerce\Catalog\Models\CatalogItem;
use Init\Commerce\Stock\Database\Seeders\RootSeeder;
use Init\Commerce\Stock\Filament\RelationManagers\CatalogItemStockLevelsRelationManager;
use Init\Commerce\Stock\Filament\RelationManagers\CatalogItemStockMovementsRelationManager;
use Init\Commerce\Stock\Filament\RootPlugin;
use Init\Commerce\Stock\Models\StockLevel;
use Init\Commerce\Stock\Models\StockMovement;
use Init\Commerce\Stock\Models\Warehouse;
use Init\Core\Database\SeederRegistry;
use Init\Core\Filament\FilamentPluginRegistry;
use Init\Parties\Models\Party;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RootServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('commerce_stock')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        if (! $this->app->environment('production')) {
            SeederRegistry::registerIfNotExists('init/commerce-stock', [
                RootSeeder::class,
            ]);
        }

        FilamentPluginRegistry::registerPlugin(RootPlugin::make());
        CatalogItemFilamentExtRegistry::registerRelationManager(CatalogItemStockLevelsRelationManager::class);
        CatalogItemFilamentExtRegistry::registerRelationManager(CatalogItemStockMovementsRelationManager::class);

        if (class_exists(\Init\Documentation\Support\DocumentationRegistry::class)) {
            \Init\Documentation\Support\DocumentationRegistry::registerPath(
                package: 'init/commerce-stock',
                slug: 'commerce/stock',
                title: 'Commerce Stock',
                path: dirname(__DIR__) . '/README.md',
                group: 'Commerce Foundation',
                sort: 30,
                summary: 'Warehouses, stock levels, reservations and movement ledger.',
            );
        }
    }

    public function packageBooted(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        Route::prefix('api/commerce')
            ->middleware('api')
            ->group(__DIR__ . '/../routes/api.php');

        CatalogItem::resolveRelationUsing('stockLevels', function (CatalogItem $catalogItem) {
            return $catalogItem->hasMany(StockLevel::class, 'catalog_item_id');
        });

        CatalogItem::resolveRelationUsing('stockMovements', function (CatalogItem $catalogItem) {
            return $catalogItem->hasMany(StockMovement::class, 'catalog_item_id');
        });

        Party::resolveRelationUsing('responsibleWarehouses', function (Party $party) {
            return $party->hasMany(Warehouse::class, 'responsible_party_id');
        });
    }
}
