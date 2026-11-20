<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'default_address', 'default_city', 'default_country'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
