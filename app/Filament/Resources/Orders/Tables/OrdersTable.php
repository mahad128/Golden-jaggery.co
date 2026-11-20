<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')->searchable()->sortable(),
                TextColumn::make('customer_name')->searchable(),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('total')->money('PKR', divideBy: 1)->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'packed' => 'Packed',
                    'shipped' => 'Shipped',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ]),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('packing_slip')
                    ->label('Packing slip')
                    ->url(fn (Order $record) => route('admin.orders.packing-slip', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
