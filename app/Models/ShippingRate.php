<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $fillable = ['scope', 'city', 'country', 'amount', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
