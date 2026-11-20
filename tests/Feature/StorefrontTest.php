<?php

use App\Mail\OrderConfirmation;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingRate;
use App\Services\OrderStatusService;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->product = Product::create([
        'slug' => 'test-gur',
        'name' => 'Test Gur',
        'type' => 'gur block',
        'short_description' => 'A test product.',
        'status' => 'in_stock',
        'images' => [],
    ]);

    $this->variant = ProductVariant::create([
        'product_id' => $this->product->id,
        'sku' => 'TEST-GUR-500',
        'size' => '500 g',
        'weight_grams' => 500,
        'price' => 500,
        'wholesale_price' => 400,
        'stock' => 3,
        'is_active' => true,
    ]);

    $this->shippingRate = ShippingRate::create([
        'scope' => 'city',
        'city' => null,
        'country' => 'Pakistan',
        'amount' => 250,
        'is_active' => true,
    ]);
});

test('storefront pages render through Inertia', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Home')->has('products'));

    $this->get('/shop')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Shop')->has('products.data', 1));

    $this->get('/products/test-gur')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Product')->where('product.slug', 'test-gur'));
});

test('guest can switch the storefront locale to Urdu', function () {
    $this->from('/')->post('/language/ur')
        ->assertSessionHas('locale', 'ur')
        ->assertRedirect('/');

    $this->withSession(['locale' => 'ur'])
        ->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('locale', 'ur'));
});

test('guest can add a product to the session cart', function () {
    $this->post('/cart/items', ['variant_id' => $this->variant->id, 'quantity' => 2])
        ->assertRedirect();

    $this->get('/cart')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Cart')->where('subtotal', 1000));
});

test('checkout creates an order atomically and queues its email', function () {
    Mail::fake();

    $response = $this->withSession(['cart' => [$this->variant->id => 2]])
        ->post('/checkout', [
            'customer_name' => 'Amina Khan',
            'phone' => '+923001234567',
            'email' => 'amina@example.test',
            'address' => 'House 10, Main Road',
            'city' => 'Lahore',
            'country' => 'Pakistan',
            'delivery_method' => 'domestic',
            'payment_method' => 'cash_on_delivery',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('/orders/');
    $order = Order::query()->firstOrFail();
    $response->assertRedirect(route('orders.confirmation', [
        'orderNumber' => $order->order_number,
        'token' => $order->confirmation_token,
    ]));
    expect($order->total)->toBe(1250);
    expect($order->items)->toHaveCount(1);
    expect($order->items->first()->stock_reserved)->toBeTrue();
    expect($this->variant->fresh()->stock)->toBe(1);
    Mail::assertQueued(OrderConfirmation::class);
});

test('order confirmation requires its unguessable token', function () {
    $order = Order::create([
        'order_number' => 'GJSAFE123',
        'confirmation_token' => str_repeat('a', 64),
        'customer_name' => 'Amina Khan',
        'phone' => '+923001234567',
        'address' => 'House 10, Main Road',
        'city' => 'Lahore',
        'country' => 'Pakistan',
        'delivery_method' => 'domestic',
        'payment_method' => 'cash_on_delivery',
        'subtotal' => 500,
        'shipping_charge' => 250,
        'total' => 750,
    ]);

    $this->get(route('orders.confirmation', $order->order_number))->assertNotFound();
    $this->get(route('orders.confirmation', ['orderNumber' => $order->order_number, 'token' => str_repeat('a', 64)]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('OrderConfirmation')
            ->where('order.order_number', 'GJSAFE123')
            ->missing('order.confirmation_token'));

    $legacyOrder = Order::create([
        'order_number' => 'GJLEGACY123',
        'customer_name' => 'Amina Khan',
        'phone' => '+923001234567',
        'address' => 'House 10, Main Road',
        'city' => 'Lahore',
        'country' => 'Pakistan',
        'delivery_method' => 'domestic',
        'payment_method' => 'cash_on_delivery',
        'subtotal' => 500,
        'shipping_charge' => 250,
        'total' => 750,
    ]);

    $this->get(route('orders.confirmation', ['orderNumber' => $legacyOrder->order_number, 'token' => 'any']))
        ->assertNotFound();
});

test('cancelling an order restores only its reserved stock', function () {
    $this->variant->update(['stock' => 1]);
    $order = Order::create([
        'order_number' => 'GJCANCEL123',
        'confirmation_token' => str_repeat('b', 64),
        'customer_name' => 'Amina Khan',
        'phone' => '+923001234567',
        'address' => 'House 10, Main Road',
        'city' => 'Lahore',
        'country' => 'Pakistan',
        'delivery_method' => 'domestic',
        'payment_method' => 'cash_on_delivery',
        'status' => 'pending',
        'subtotal' => 1000,
        'shipping_charge' => 250,
        'total' => 1250,
    ]);
    $order->items()->create([
        'product_id' => $this->product->id,
        'product_variant_id' => $this->variant->id,
        'product_name' => $this->product->name,
        'variant_name' => '500 g',
        'quantity' => 2,
        'unit_price' => 500,
        'line_total' => 1000,
        'stock_reserved' => true,
    ]);

    app(OrderStatusService::class)->update($order, 'cancelled', null);

    expect($this->variant->fresh()->stock)->toBe(3);
    expect($order->items()->first()->stock_reserved)->toBeFalse();
});

test('checkout refuses to oversell inventory', function () {
    $response = $this->withSession(['cart' => [$this->variant->id => 4]])
        ->from('/checkout')
        ->post('/checkout', [
            'customer_name' => 'Amina Khan',
            'phone' => '+923001234567',
            'address' => 'House 10, Main Road',
            'city' => 'Lahore',
            'country' => 'Pakistan',
            'delivery_method' => 'domestic',
            'payment_method' => 'cash_on_delivery',
        ]);

    $response->assertSessionHasErrors('cart');
    expect(Order::query()->count())->toBe(0);
    expect($this->variant->fresh()->stock)->toBe(3);
});

test('checkout requires a configured destination shipping rate', function () {
    $this->shippingRate->delete();

    $this->withSession(['cart' => [$this->variant->id => 1]])
        ->from('/checkout')
        ->post('/checkout', [
            'customer_name' => 'Amina Khan',
            'phone' => '+923001234567',
            'address' => 'House 10, Main Road',
            'city' => 'Lahore',
            'country' => 'Pakistan',
            'delivery_method' => 'domestic',
            'payment_method' => 'cash_on_delivery',
        ])
        ->assertSessionHasErrors('city');

    expect(Order::query()->count())->toBe(0);
});

test('discount codes are checked against the current cart subtotal', function () {
    DiscountCode::create([
        'code' => 'WELCOME10',
        'type' => 'percent',
        'value' => 10,
        'minimum_order' => 500,
        'is_active' => true,
    ]);

    $this->withSession(['cart' => [$this->variant->id => 2]])
        ->postJson('/checkout/discount', ['discount_code' => 'welcome10'])
        ->assertOk()
        ->assertJson(['code' => 'WELCOME10', 'discount' => 100]);
});

test('orders can only be tracked with their order number and checkout phone', function () {
    $order = Order::create([
        'order_number' => 'GJTEST123',
        'confirmation_token' => str_repeat('c', 64),
        'customer_name' => 'Amina Khan',
        'phone' => '+923001234567',
        'address' => 'House 10, Main Road',
        'city' => 'Lahore',
        'country' => 'Pakistan',
        'delivery_method' => 'domestic',
        'payment_method' => 'cash_on_delivery',
        'status' => 'shipped',
        'subtotal' => 500,
        'shipping_charge' => 250,
        'total' => 750,
    ]);

    $this->post('/track-order', ['order_number' => $order->order_number, 'phone' => '+923001234567'])
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('TrackOrder')->where('order.order_number', 'GJTEST123'));

    $this->post('/track-order', ['order_number' => $order->order_number, 'phone' => '+923009999999'])
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('TrackOrder')->where('notFound', true));
});
