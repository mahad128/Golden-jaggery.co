<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Sizes, prices & stock';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sku')->required()->unique(ignoreRecord: true),
            TextInput::make('size')->required()->placeholder('500 g'),
            TextInput::make('weight_grams')->numeric()->required()->minValue(1),
            TextInput::make('price')->numeric()->required()->minValue(1)->prefix('PKR'),
            TextInput::make('wholesale_price')->numeric()->minValue(1)->prefix('PKR'),
            TextInput::make('stock')->numeric()->required()->minValue(0)->default(0),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('size')
            ->columns([
                TextColumn::make('size')->sortable(),
                TextColumn::make('price')->money('PKR', divideBy: 1)->sortable(),
                TextColumn::make('wholesale_price')->money('PKR', divideBy: 1),
                TextColumn::make('stock')->numeric()->sortable(),
                IconColumn::make('is_active')->boolean(),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
