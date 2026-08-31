<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sitata Insurance API Configuration
    |--------------------------------------------------------------------------
    */
    'api_key' => env('SITATA_API_KEY', ''),
    'organization_id' => env('SITATA_ORGANIZATION_ID', ''),
    'public_token' => env('SITATA_PUBLIC_TOKEN', ''),
    'api_url' => env('SITATA_API_URL', 'https://staging.sitata.com/api/v2'),
    'sandbox' => env('SITATA_SANDBOX', true),

    /*
    |--------------------------------------------------------------------------
    | Business Rules & Margin Settings
    |--------------------------------------------------------------------------
    */
    'enabled' => env('INSURANCE_ENABLED', true),
    'currency' => env('INSURANCE_CURRENCY', 'SAR'),
    'default_margin_type' => env('INSURANCE_MARGIN_TYPE', 'percentage'), // 'percentage' or 'fixed'
    'default_margin_value' => env('INSURANCE_MARGIN_VALUE', 20), // 20% or 20 SAR
    'min_price' => env('INSURANCE_MIN_PRICE', 35), // minimum price in SAR
    'emergency_phone' => env('INSURANCE_EMERGENCY_PHONE', '+1-800-456-7890'),
    'emergency_email' => env('INSURANCE_EMERGENCY_EMAIL', 'assistance@sitata.com'),
];
