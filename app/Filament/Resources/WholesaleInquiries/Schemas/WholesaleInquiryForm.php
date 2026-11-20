<?php

namespace App\Filament\Resources\WholesaleInquiries\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WholesaleInquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('business_name')->disabled(),
                TextInput::make('contact_name')->disabled(),
                TextInput::make('email')->disabled(),
                TextInput::make('phone')->disabled(),
                TextInput::make('city')->disabled(),
                TextInput::make('monthly_quantity_kg')->disabled(),
                Textarea::make('message')->disabled()->columnSpanFull(),
                Select::make('status')->options(['new' => 'New', 'contacted' => 'Contacted', 'quoted' => 'Quoted', 'closed' => 'Closed'])->required(),
            ]);
    }
}
