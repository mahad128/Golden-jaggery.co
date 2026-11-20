<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\ContactMessageRequest;
use App\Http\Requests\TrackOrderRequest;
use App\Http\Requests\WholesaleInquiryRequest;
use App\Mail\OrderConfirmation;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\ShippingRate;
use App\Models\SiteSetting;
use App\Models\WholesaleInquiry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class StorefrontController extends Controller
{
    public function home(): Response
    {
        return Inertia::render('Home', [
            'products' => Product::query()->with('activeVariants')->where('is_featured', true)->where('status', '!=', 'out_of_season')->take(4)->get(),
            'reviews' => Review::query()->where('is_published', true)->latest()->take(3)->get(),
            'posts' => BlogPost::query()->where('is_published', true)->latest('published_at')->take(3)->get(),
            'content' => SiteSetting::query()->whereIn('key', ['hero_title', 'hero_subtitle', 'story_title', 'story_text'])->pluck('value', 'key'),
        ]);
    }

    public function shop(Request $request): Response
    {
        $products = Product::query()
            ->with('activeVariants')
            ->when($request->filled('type'), fn (Builder $query) => $query->where('type', $request->string('type')))
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $search = '%'.$request->string('q')->trim().'%';
                $query->where(fn (Builder $nested) => $nested
                    ->where('name', 'like', $search)
                    ->orWhere('name_ur', 'like', $search)
                    ->orWhere('short_description', 'like', $search));
            })
            ->when($request->filled('weight'), fn (Builder $query) => $query->whereHas('activeVariants', fn (Builder $variants) => $variants->where('weight_grams', $request->integer('weight'))))
            ->when($request->filled('max_price'), fn (Builder $query) => $query->whereHas('activeVariants', fn (Builder $variants) => $variants->where('price', '<=', $request->integer('max_price'))))
            ->when($request->input('sort') === 'price_low', fn (Builder $query) => $query->withMin('activeVariants', 'price')->orderBy('active_variants_min_price'))
            ->when($request->input('sort') === 'price_high', fn (Builder $query) => $query->withMax('activeVariants', 'price')->orderByDesc('active_variants_max_price'))
            ->when(! in_array($request->input('sort'), ['price_low', 'price_high'], true), fn (Builder $query) => $query->orderByDesc('is_featured')->orderBy('name'))
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Shop', [
            'products' => $products,
            'filters' => $request->only(['q', 'type', 'weight', 'max_price', 'sort']),
            'types' => Product::query()->distinct()->orderBy('type')->pluck('type'),
        ]);
    }

    public function product(string $slug): Response
    {
        $product = Product::query()
            ->with(['activeVariants', 'reviews' => fn ($query) => $query->where('is_published', true)->latest()])
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render('Product', ['product' => $product]);
    }

    public function about(): Response
    {
        return Inertia::render('About');
    }

    public function blog(): Response
    {
        return Inertia::render('Blog/Index', [
            'posts' => BlogPost::query()->where('is_published', true)->latest('published_at')->paginate(9),
        ]);
    }

    public function blogPost(string $slug): Response
    {
        return Inertia::render('Blog/Show', [
            'post' => BlogPost::query()->where('is_published', true)->where('slug', $slug)->firstOrFail(),
        ]);
    }

    public function contact(): Response
    {
        return Inertia::render('Contact');
    }

    public function submitContact(ContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::create($request->validated());

        return back()->with('success', 'Thanks for reaching out. Our family will get back to you soon.');
    }

    public function wholesale(): Response
    {
        return Inertia::render('Wholesale');
    }

    public function submitWholesale(WholesaleInquiryRequest $request): RedirectResponse
    {
        WholesaleInquiry::create($request->validated());

        return back()->with('success', 'Your wholesale inquiry has been received.');
    }

    public function cart(): Response
    {
        $items = $this->cartItems();

        return Inertia::render('Cart', [
            'items' => $items,
            'subtotal' => $items->sum('line_total'),
        ]);
    }

    public function addToCart(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);
        $variant = ProductVariant::query()->with('product')->where('is_active', true)->findOrFail($data['variant_id']);
        $quantity = $data['quantity'] ?? 1;
        $cart = $request->session()->get('cart', []);
        $newQuantity = ($cart[$variant->id] ?? 0) + $quantity;

        if ($variant->product->status === 'out_of_season') {
            throw ValidationException::withMessages(['variant_id' => 'This product is currently out of season.']);
        }

        if ($variant->product->status === 'in_stock' && $newQuantity > $variant->stock) {
            throw ValidationException::withMessages(['variant_id' => 'There is not enough stock for that quantity.']);
        }

        $cart[$variant->id] = $newQuantity;
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Added to your cart.');
    }

    public function updateCart(Request $request, int $variant): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:30']]);
        $productVariant = ProductVariant::query()->with('product')->where('is_active', true)->findOrFail($variant);

        if ($productVariant->product->status === 'out_of_season') {
            throw ValidationException::withMessages(['quantity' => 'This product is currently out of season.']);
        }

        if ($productVariant->product->status === 'in_stock' && $data['quantity'] > $productVariant->stock) {
            throw ValidationException::withMessages(['quantity' => 'There is not enough stock for that quantity.']);
        }

        $cart = $request->session()->get('cart', []);
        abort_unless(array_key_exists($variant, $cart), 404);
        $cart[$variant] = $data['quantity'];
        $request->session()->put('cart', $cart);

        return back();
    }

    public function removeFromCart(Request $request, int $variant): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$variant]);
        $request->session()->put('cart', $cart);

        return back();
    }

    public function checkout(): Response|RedirectResponse
    {
        $items = $this->cartItems();

        if ($items->isEmpty()) {
            return redirect()->route('shop')->with('success', 'Choose something delicious before checking out.');
        }

        return Inertia::render('Checkout', [
            'items' => $items,
            'subtotal' => $items->sum('line_total'),
            'shippingRates' => ShippingRate::query()->where('is_active', true)->get(['scope', 'city', 'country', 'amount']),
            'paymentMethods' => ['cash_on_delivery', 'bank_transfer'],
        ]);
    }

    public function quoteDiscount(Request $request): JsonResponse
    {
        $data = $request->validate(['discount_code' => ['required', 'string', 'max:60']]);
        $items = $this->cartItems();
        $subtotal = $items->sum('line_total');
        $discount = $this->validatedDiscount($data['discount_code'], $subtotal);
        $amount = $discount->type === 'percent'
            ? (int) floor($subtotal * min($discount->value, 100) / 100)
            : min($discount->value, $subtotal);

        return response()->json(['code' => $discount->code, 'discount' => $amount]);
    }

    public function placeOrder(CheckoutRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $cart = $request->session()->get('cart', []);

        if ($cart === []) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        if ($data['delivery_method'] === 'domestic' && strcasecmp($data['country'], 'Pakistan') !== 0) {
            throw ValidationException::withMessages(['country' => 'Domestic delivery is available within Pakistan.']);
        }

        if ($data['delivery_method'] === 'international' && $data['payment_method'] === 'cash_on_delivery') {
            throw ValidationException::withMessages(['payment_method' => 'Cash on delivery is available for Pakistan deliveries only.']);
        }

        $order = DB::transaction(function () use ($cart, $data): Order {
            $variants = ProductVariant::query()
                ->with('product')
                ->whereIn('id', array_keys($cart))
                ->where('is_active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($variants->count() !== count($cart)) {
                throw ValidationException::withMessages(['cart' => 'A cart item is no longer available. Please review your cart.']);
            }

            $subtotal = 0;
            foreach ($cart as $variantId => $quantity) {
                $variant = $variants->get($variantId);

                if ($variant->product->status === 'out_of_season') {
                    throw ValidationException::withMessages(['cart' => "{$variant->product->name} is currently out of season."]);
                }

                if ($variant->product->status === 'in_stock' && $variant->stock < $quantity) {
                    throw ValidationException::withMessages(['cart' => "There is not enough stock for {$variant->product->name}."]);
                }

                $subtotal += $variant->price * $quantity;
            }

            $rate = $this->shippingRate($data);
            if (! $rate) {
                throw ValidationException::withMessages(['city' => 'Shipping for this destination is not configured yet. Please contact us for a quote.']);
            }

            $discount = $this->validatedDiscount($data['discount_code'] ?? null, $subtotal);
            $discountAmount = $discount?->type === 'percent'
                ? (int) floor($subtotal * min($discount->value, 100) / 100)
                : min($discount?->value ?? 0, $subtotal);

            $customer = Customer::query()->firstOrNew(['phone' => $data['phone']]);
            $customer->fill([
                'name' => $data['customer_name'],
                'email' => $data['email'] ?? null,
                'default_address' => $data['address'],
                'default_city' => $data['city'],
                'default_country' => $data['country'],
            ])->save();

            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'GJ'.now()->format('ymd').Str::upper(Str::random(6)),
                'confirmation_token' => Str::random(64),
                'customer_name' => $data['customer_name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'],
                'city' => $data['city'],
                'country' => $data['country'],
                'delivery_method' => $data['delivery_method'],
                'payment_method' => $data['payment_method'],
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_charge' => $rate->amount,
                'discount_amount' => $discountAmount,
                'total' => max(0, $subtotal + $rate->amount - $discountAmount),
                'discount_code' => $discount?->code,
                'customer_note' => $data['customer_note'] ?? null,
            ]);

            foreach ($cart as $variantId => $quantity) {
                $variant = $variants->get($variantId);
                $order->items()->create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name,
                    'variant_name' => $variant->size,
                    'quantity' => $quantity,
                    'unit_price' => $variant->price,
                    'line_total' => $variant->price * $quantity,
                    'stock_reserved' => $variant->product->status === 'in_stock',
                ]);

                if ($variant->product->status === 'in_stock') {
                    $variant->decrement('stock', $quantity);
                }
            }

            if ($discount) {
                $discount->increment('redemptions');
            }

            return $order;
        });

        $request->session()->forget('cart');

        if ($order->email) {
            Mail::to($order->email)->queue(new OrderConfirmation($order));
        }

        return redirect()->route('orders.confirmation', [
            'orderNumber' => $order->order_number,
            'token' => $order->confirmation_token,
        ]);
    }

    public function confirmation(Request $request, string $orderNumber): Response
    {
        $order = Order::query()->with('items')->where('order_number', $orderNumber)->firstOrFail();
        abort_unless(
            $order->confirmation_token && hash_equals($order->confirmation_token, (string) $request->query('token')),
            404
        );

        return Inertia::render('OrderConfirmation', ['order' => $order]);
    }

    public function track(): Response
    {
        return Inertia::render('TrackOrder');
    }

    public function findOrder(TrackOrderRequest $request): Response
    {
        $order = Order::query()
            ->with('items')
            ->where('order_number', $request->validated('order_number'))
            ->where('phone', $request->validated('phone'))
            ->first();

        return Inertia::render('TrackOrder', [
            'order' => $order,
            'notFound' => ! $order,
        ]);
    }

    private function cartItems(): Collection
    {
        $cart = request()->session()->get('cart', []);

        if ($cart === []) {
            return collect();
        }

        $variants = ProductVariant::query()
            ->with('product')
            ->whereIn('id', array_keys($cart))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        return collect($cart)->map(function (int $quantity, int|string $id) use ($variants): ?array {
            $variant = $variants->get($id);
            if (! $variant) {
                return null;
            }

            return [
                'variant' => $variant,
                'quantity' => $quantity,
                'line_total' => $variant->price * $quantity,
            ];
        })->filter()->values();
    }

    private function shippingRate(array $data): ?ShippingRate
    {
        if ($data['delivery_method'] === 'domestic') {
            $rates = ShippingRate::query()
                ->where('scope', 'city')
                ->where('country', 'Pakistan')
                ->where('is_active', true)
                ->get();

            return $rates->first(fn (ShippingRate $rate) => $rate->city && mb_strtolower($rate->city) === mb_strtolower($data['city']))
                ?? $rates->first(fn (ShippingRate $rate) => $rate->city === null);
        }

        return ShippingRate::query()
            ->where('scope', 'country')
            ->whereRaw('LOWER(country) = ?', [mb_strtolower($data['country'])])
            ->where('is_active', true)
            ->first();
    }

    private function validatedDiscount(?string $code, int $subtotal): ?DiscountCode
    {
        if (! $code) {
            return null;
        }

        $discount = DiscountCode::query()->where('code', Str::upper(trim($code)))->lockForUpdate()->first();
        $now = now();

        if (
            ! $discount
            || ! $discount->is_active
            || ($discount->starts_at && $discount->starts_at->isFuture())
            || ($discount->expires_at && $discount->expires_at->isPast())
            || ($discount->max_redemptions && $discount->redemptions >= $discount->max_redemptions)
            || $subtotal < $discount->minimum_order
        ) {
            throw ValidationException::withMessages(['discount_code' => 'This discount code is not valid for this order.']);
        }

        return $discount;
    }
}
