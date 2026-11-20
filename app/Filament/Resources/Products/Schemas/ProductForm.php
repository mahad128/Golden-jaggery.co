<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Components\CloudinaryFileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(160),
                TextInput::make('name_ur')->label('Name (Urdu)')->maxLength(160),
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(180),
                Select::make('type')->options([
                    'gur block' => 'Gur block',
                    'gur powder' => 'Gur powder',
                    'shakkar' => 'Shakkar',
                    'flavoured gur' => 'Flavoured gur',
                ])->required(),
                Select::make('status')->options([
                    'in_stock' => 'In stock',
                    'pre_order' => 'Pre-order',
                    'out_of_season' => 'Out of season',
                ])->required()->default('in_stock'),
                Textarea::make('short_description')->required()->rows(3)->columnSpanFull(),
                Textarea::make('description')->rows(6)->columnSpanFull(),
                Textarea::make('description_ur')->label('Description (Urdu)')->rows(6)->columnSpanFull(),
                TagsInput::make('benefits')->separator(',')->columnSpanFull(),
                TagsInput::make('how_to_use')->label('How to use')->separator(',')->columnSpanFull(),
                CloudinaryFileUpload::make('images', 'products', multiple: true)
                    ->label('Product photos')
                    ->columnSpanFull(),
                TextInput::make('seo_description')->maxLength(320)->columnSpanFull(),
                Toggle::make('is_featured')->default(false),
            ]);
    }
}
