<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('order_number')->disabled(),
                TextInput::make('customer_name')->disabled(),
                TextInput::make('phone')->disabled(),
                TextInput::make('email')->disabled(),
                Select::make('status')->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'packed' => 'Packed',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ])->required(),
                TextInput::make('tracking_number')->maxLength(120),
            ]);
    }
}
