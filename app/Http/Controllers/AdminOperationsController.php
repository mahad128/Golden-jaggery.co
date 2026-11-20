<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminOperationsController extends Controller
{
    public function exportOrders(Request $request): StreamedResponse
    {
        abort_unless($request->user()?->is_admin, 403);

        return response()->streamDownload(function (): void {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, ['Order number', 'Customer', 'Phone', 'Email', 'City', 'Country', 'Status', 'Subtotal PKR', 'Shipping PKR', 'Total PKR', 'Created at']);

            Order::query()->orderByDesc('created_at')->chunk(500, function ($orders) use ($stream): void {
                foreach ($orders as $order) {
                    fputcsv($stream, array_map($this->spreadsheetSafe(...), [
                        $order->order_number,
                        $order->customer_name,
                        $order->phone,
                        $order->email,
                        $order->city,
                        $order->country,
                        $order->status,
                        $order->subtotal,
                        $order->shipping_charge,
                        $order->total,
                        $order->created_at->toIso8601String(),
                    ]));
                }
            });

            fclose($stream);
        }, 'golden-jaggery-orders.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function packingSlip(Request $request, Order $order)
    {
        abort_unless($request->user()?->is_admin, 403);

        return view('admin.packing-slip', ['order' => $order->load('items')]);
    }

    private function spreadsheetSafe(string|int|null $value): string|int|null
    {
        if (is_string($value) && preg_match('/^[=+\-@]/', $value)) {
            return "'".$value;
        }

        return $value;
    }
}
