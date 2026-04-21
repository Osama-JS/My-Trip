<?php

return [
    'base_url' => env('HYPERPAY_BASE_URL', 'https://test.oppwa.com/v1/'),
    'widget_url' => env('HYPERPAY_WIDGET_URL', 'test.oppwa.com'),
    'access_token' => env('HYPERPAY_ACCESS_TOKEN'),

    'entity_ids' => [
        'mada' => env('HYPERPAY_ENTITY_ID_MADA'),
        'visa_master' => env('HYPERPAY_ENTITY_ID_VISA_MASTER'),
        'apple_pay' => env('HYPERPAY_ENTITY_ID_APPLE'),
    ],

    'currency' => env('HYPERPAY_CURRENCY', 'SAR'),

    'test_mode' => env('HYPERPAY_TEST_MODE', false),
    'merchant_url' => env('HYPERPAY_MERCHANT_URL', env('APP_URL')),
    'merchant_phone' => env('HYPERPAY_MERCHANT_PHONE'),
];
