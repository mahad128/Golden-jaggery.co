<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WholesaleInquiry extends Model
{
    protected $fillable = [
        'business_name',
        'contact_name',
        'email',
        'phone',
        'city',
        'monthly_quantity_kg',
        'message',
        'status',
    ];
}
