<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    private const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['packed', 'cancelled'],
        'packed' => ['shipped', 'cancelled'],
        'shipped' => ['delivered', 'cancelled'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public function update(Order $order, string $nextStatus, ?string $trackingNumber): Order
    {
        return DB::transaction(function () use ($order, $nextStatus, $trackingNumber): Order {
            $lockedOrder = Order::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($order->id);

            if (
                $nextStatus !== $lockedOrder->status
                && ! in_array($nextStatus, self::TRANSITIONS[$lockedOrder->status] ?? [], true)
            ) {
                throw ValidationException::withMessages(['status' => 'This order cannot move to the selected status.']);
            }

            if ($nextStatus === 'shipped' && blank($trackingNumber)) {
                throw ValidationException::withMessages(['tracking_number' => 'Enter the carrier tracking number before marking this order as shipped.']);
            }

            if ($nextStatus === 'cancelled' && $lockedOrder->status !== 'cancelled') {
                foreach ($lockedOrder->items as $item) {
                    if (! $item->stock_reserved || ! $item->product_variant_id) {
                        continue;
                    }

                    $variant = ProductVariant::query()->lockForUpdate()->find($item->product_variant_id);
                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                    }

                    $item->update(['stock_reserved' => false]);
                }
            }

            $lockedOrder->update([
                'status' => $nextStatus,
                'tracking_number' => $trackingNumber,
            ]);

            return $lockedOrder->refresh();
        });
    }
}
