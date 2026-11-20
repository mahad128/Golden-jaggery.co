<h1>Thank you for your order, {{ $order->customer_name }}!</h1>
<p>We have received order <strong>{{ $order->order_number }}</strong>.</p>
<p>We will contact you at {{ $order->phone }} to confirm delivery.</p>
<ul>
    @foreach ($order->items as $item)
        <li>{{ $item->product_name }} ({{ $item->variant_name }}) × {{ $item->quantity }} — PKR {{ number_format($item->line_total) }}</li>
    @endforeach
</ul>
<p>Total: <strong>PKR {{ number_format($order->total) }}</strong></p>
<p>View your confirmation at {{ route('orders.confirmation', ['orderNumber' => $order->order_number, 'token' => $order->confirmation_token]) }}.</p>
<p>Track your order at {{ route('orders.track') }} using your order number and phone.</p>
