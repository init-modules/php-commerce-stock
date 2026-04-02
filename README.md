# init/commerce-stock

Commerce stock and warehouse foundation package

## Что решает пакет

- новый reusable package в монорепе Init PHP
- совместимость с `.agents` knowledge base и package conventions
- предсказуемую структуру для service provider, config, database, tests и docker workflow

## Установка

```bash
composer require init/commerce-stock
```

После подключения:

- обнови зависимости проекта
- примени миграции, если пакет использует БД
- настрой конфиг пакета, если требуется

## Использование

Пакет включает:

- склады, ledger движений и projection остатков
- actions `AdjustStock`, `AllocateStockForOrder`, `ReleaseStock`
- stock allocation на placed order работает как reservation, а не как немедленное списание on-hand
- read-only ресурс движений и ресурс остатков для ручной корректировки
- relation managers на catalog item resource через `CatalogItemFilamentExtRegistry`
- demo seeders, которые запускаются только вне production

## Структура

- path: `commerce-foundation/commerce-stock`
- actions:
- `AdjustStock`
- `AllocateStockForOrder`
- `ReleaseStock`

## Разработка

- package workflow по умолчанию идет через Docker / Docker Compose
- package tests запускаются через `make setup && make test`
- action test stubs генерируются через `.agents/scripts/generate_action_test_stubs.py`
- архитектурные и UI правила зафиксированы в `.agents/conventions/`
- package checks лежат в `tests/Feature/`
- app-level Filament integration checks добавлены в `laravel/tests/Feature/Commerce/CommerceAdminIntegrationTest.php`
