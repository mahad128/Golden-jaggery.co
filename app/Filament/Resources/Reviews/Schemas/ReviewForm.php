<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Models\Product;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')->options(Product::query()->orderBy('name')->pluck('name', 'id'))->searchable(),
                TextInput::make('customer_name')->required()->maxLength(120),
                TextInput::make('rating')->numeric()->required()->minValue(1)->maxValue(5),
                Textarea::make('body')->required()->rows(5)->columnSpanFull(),
                Toggle::make('is_published')->default(false),
            ]);
    }
}
