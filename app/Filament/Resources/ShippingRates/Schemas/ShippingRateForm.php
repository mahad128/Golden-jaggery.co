<?php

namespace App\Filament\Resources\ShippingRates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ShippingRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('scope')->options(['city' => 'Pakistan city', 'country' => 'International country'])->required()->live(),
                TextInput::make('city')->visible(fn ($get) => $get('scope') === 'city')->placeholder('Leave blank for the country-wide domestic rate'),
                TextInput::make('country')->required()->default('Pakistan'),
                TextInput::make('amount')->numeric()->required()->minValue(0)->prefix('PKR'),
                Toggle::make('is_active')->default(true),
            ]);
    }
}
