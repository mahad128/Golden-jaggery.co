<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WholesaleInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name' => ['required', 'string', 'max:160'],
            'contact_name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'city' => ['required', 'string', 'max:120'],
            'monthly_quantity_kg' => ['required', 'integer', 'min:10', 'max:100000'],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
