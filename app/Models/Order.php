<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $hidden = ['confirmation_token'];

    protected $fillable = [
        'customer_id',
        'order_number',
        'confirmation_token',
        'customer_name',
        'phone',
        'email',
        'address',
        'city',
        'country',
        'delivery_method',
        'payment_method',
        'status',
        'tracking_number',
        'subtotal',
        'shipping_charge',
        'discount_amount',
        'total',
        'discount_code',
        'customer_note',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
