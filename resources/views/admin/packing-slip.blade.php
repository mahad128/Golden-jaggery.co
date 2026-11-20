<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Packing slip — {{ $order->order_number }}</title>
    <style>
        body { max-width: 760px; margin: 32px auto; padding: 0 24px; color: #3b2a1a; font: 15px/1.5 Arial, sans-serif; }
        h1, h2 { font-family: Georgia, serif; }
        header, .row { display: flex; justify-content: space-between; gap: 20px; }
        table { width: 100%; margin-top: 24px; border-collapse: collapse; }
        th, td { padding: 10px 6px; border-bottom: 1px solid #ddd; text-align: left; }
        .right { text-align: right; }
        @media print { .print { display: none; } body { margin: 0; } }
    </style>
</head>
<body>
    <button class="print" onclick="window.print()">Print packing slip</button>
    <header>
        <div><h1>Golden Jaggery Co</h1><p>Pure Desi Jaggery & Shakkar, Fresh from Our Family to Yours</p></div>
        <div><h2>Packing slip</h2><strong>{{ $order->order_number }}</strong><br>{{ $order->created_at->format('d M Y') }}</div>
    </header>
    <hr>
    <div class="row">
        <div><strong>Ship to</strong><br>{{ $order->customer_name }}<br>{{ $order->address }}<br>{{ $order->city }}, {{ $order->country }}<br>{{ $order->phone }}</div>
        <div><strong>Delivery</strong><br>{{ ucfirst($order->delivery_method) }}<br>Payment: {{ str($order->payment_method)->replace('_', ' ')->title() }}</div>
    </div>
    <table>
        <thead><tr><th>Product</th><th>Pack</th><th class="right">Qty</th></tr></thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr><td>{{ $item->product_name }}</td><td>{{ $item->variant_name }}</td><td class="right">{{ $item->quantity }}</td></tr>
            @endforeach
        </tbody>
    </table>
    @if ($order->customer_note)<p><strong>Customer note:</strong> {{ $order->customer_note }}</p>@endif
    <p>Thank you for supporting a family-run business.</p>
</body>
</html>
