<?php

namespace Init\Commerce\Stock\Filament\Resources\Warehouses\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Init\Commerce\Stock\Enums\WarehouseStatus;
use Init\Parties\Models\Party;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Склад')
                ->schema([
                    TextInput::make('code')
                        ->label('Код')
                        ->required()
                        ->unique(ignoreRecord: true),
                    TextInput::make('name')
                        ->label('Название')
                        ->required(),
                    Select::make('status')
                        ->label('Статус')
                        ->options(WarehouseStatus::options())
                        ->default(WarehouseStatus::ACTIVE->value)
                        ->required()
                        ->native(false),
                    Toggle::make('is_default')
                        ->label('Склад по умолчанию'),
                    Select::make('responsible_party_id')
                        ->label('Ответственная party')
                        ->options(fn (): array => Party::query()->pluck('id', 'id')->all())
                        ->searchable(),
                    KeyValue::make('meta')
                        ->label('Meta')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
