<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('name_ur')->nullable();
            $table->string('type')->index();
            $table->text('short_description');
            $table->longText('description')->nullable();
            $table->longText('description_ur')->nullable();
            $table->json('benefits')->nullable();
            $table->json('how_to_use')->nullable();
            $table->json('images')->nullable();
            $table->string('status')->default('in_stock')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->string('seo_description', 320)->nullable();
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('size');
            $table->unsignedInteger('weight_grams');
            $table->unsignedInteger('price');
            $table->unsignedInteger('wholesale_price')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['product_id', 'is_active']);
        });

        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->string('scope')->default('city');
            $table->string('city')->nullable();
            $table->string('country');
            $table->unsignedInteger('amount');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['scope', 'country', 'city', 'is_active']);
        });

        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type');
            $table->unsignedInteger('value');
            $table->unsignedInteger('minimum_order')->default(0);
            $table->unsignedInteger('max_redemptions')->nullable();
            $table->unsignedInteger('redemptions')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 40)->index();
            $table->string('email')->nullable()->index();
            $table->text('default_address')->nullable();
            $table->string('default_city')->nullable();
            $table->string('default_country')->default('Pakistan');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('phone', 40)->index();
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('city')->nullable();
            $table->string('country')->default('Pakistan');
            $table->string('delivery_method')->default('domestic');
            $table->string('payment_method')->default('cash_on_delivery');
            $table->string('status')->default('pending')->index();
            $table->string('tracking_number')->nullable();
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('shipping_charge');
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('total');
            $table->string('discount_code')->nullable();
            $table->text('customer_note')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('variant_name');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger('line_total');
            $table->timestamps();
        });

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->unsignedTinyInteger('rating');
            $table->text('body');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('wholesale_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('contact_name');
            $table->string('email')->nullable();
            $table->string('phone', 40);
            $table->string('city');
            $table->unsignedInteger('monthly_quantity_kg');
            $table->text('message')->nullable();
            $table->string('status')->default('new')->index();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_read')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_ur')->nullable();
            $table->string('category')->default('Recipes');
            $table->string('excerpt');
            $table->longText('body');
            $table->string('image')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('wholesale_inquiries');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('discount_codes');
        Schema::dropIfExists('shipping_rates');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};
