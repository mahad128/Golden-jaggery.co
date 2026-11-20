<?php

namespace App\Filament\Resources\DiscountCodes\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DiscountCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')->required()->unique(ignoreRecord: true)->maxLength(60)->dehydrateStateUsing(fn (?string $state) => strtoupper(trim($state ?? ''))),
                Select::make('type')->options(['percent' => 'Percentage', 'fixed' => 'Fixed PKR amount'])->required(),
                TextInput::make('value')->numeric()->required()->minValue(1),
                TextInput::make('minimum_order')->numeric()->required()->minValue(0)->default(0)->prefix('PKR'),
                TextInput::make('max_redemptions')->numeric()->minValue(1),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('expires_at'),
                Toggle::make('is_active')->default(true),
            ]);
    }
}
