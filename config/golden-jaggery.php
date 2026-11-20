<?php

return [
    'name' => env('BRAND_NAME', 'Golden Jaggery Co'),
    'tagline' => env('BRAND_TAGLINE', 'Pure. Natural. Chemical-free jaggery that nourishes your body.'),
    'location' => env('BRAND_LOCATION', 'Update with village / city, Pakistan'),
    'phone' => env('BRAND_PHONE', 'Update with phone / WhatsApp number'),
    'email' => env('BRAND_EMAIL', 'Update with business email'),
    'whatsapp_enabled' => env('BRAND_WHATSAPP_ENABLED', true),
    'whatsapp_number' => env('BRAND_WHATSAPP_NUMBER', ''),
    'bank_transfer_instructions' => env('BANK_TRANSFER_INSTRUCTIONS', 'Add your bank / transfer instructions in Filament site settings.'),
    'currency' => 'PKR',
    'colors' => [
        'amber' => '#C8922E',
        'green' => '#4A6B2A',
        'cream' => '#FAF0E6',
        'brown' => '#1E1E1E',
    ],
];
