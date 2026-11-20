<?php

use App\Http\Controllers\AdminOperationsController;
use App\Http\Controllers\StorefrontController;
use App\Models\BlogPost;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/shop', [StorefrontController::class, 'shop'])->name('shop');
Route::get('/products/{slug}', [StorefrontController::class, 'product'])->name('products.show');
Route::get('/about', [StorefrontController::class, 'about'])->name('about');
Route::get('/wholesale', [StorefrontController::class, 'wholesale'])->name('wholesale');
Route::post('/wholesale', [StorefrontController::class, 'submitWholesale'])->middleware('throttle:5,1')->name('wholesale.submit');
Route::get('/contact', [StorefrontController::class, 'contact'])->name('contact');
Route::post('/contact', [StorefrontController::class, 'submitContact'])->middleware('throttle:5,1')->name('contact.submit');
Route::get('/recipes', [StorefrontController::class, 'blog'])->name('blog.index');
Route::get('/recipes/{slug}', [StorefrontController::class, 'blogPost'])->name('blog.show');
Route::get('/cart', [StorefrontController::class, 'cart'])->name('cart');
Route::post('/cart/items', [StorefrontController::class, 'addToCart'])->middleware('throttle:30,1')->name('cart.add');
Route::patch('/cart/items/{variant}', [StorefrontController::class, 'updateCart'])->name('cart.update');
Route::delete('/cart/items/{variant}', [StorefrontController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/checkout', [StorefrontController::class, 'checkout'])->name('checkout');
Route::post('/checkout/discount', [StorefrontController::class, 'quoteDiscount'])->middleware('throttle:10,1')->name('checkout.discount');
Route::post('/checkout', [StorefrontController::class, 'placeOrder'])->middleware('throttle:10,1')->name('checkout.place');
Route::get('/orders/{orderNumber}/confirmation', [StorefrontController::class, 'confirmation'])->name('orders.confirmation');
Route::get('/track-order', [StorefrontController::class, 'track'])->name('orders.track');
Route::post('/track-order', [StorefrontController::class, 'findOrder'])->middleware('throttle:10,1')->name('orders.find');
Route::post('/language/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, ['en', 'ur'], true), 404);
    $request->session()->put('locale', $locale);

    return back();
})->name('language.switch');
Route::middleware('auth')->prefix('admin/operations')->name('admin.orders.')->group(function () {
    Route::get('/export', [AdminOperationsController::class, 'exportOrders'])->name('export');
    Route::get('/{order}/packing-slip', [AdminOperationsController::class, 'packingSlip'])->name('packing-slip');
});

Route::get('/sitemap.xml', function () {
    return response()->view('sitemap', [
        'products' => Product::query()->select('slug', 'updated_at')->get(),
        'posts' => BlogPost::query()->where('is_published', true)->select('slug', 'updated_at')->get(),
    ])->header('Content-Type', 'application/xml');
})->name('sitemap');
