<?php

return [
    'merchant_id' => env('NAGAD_MERCHANT_ID',''),
    'merchant_number' => env('NAGAD_MERCHANT_NUMBER',''),
    'pg_public_key' => env('NAGAD_PG_PUBLIC_KEY',''),
    'merchant_private_key' => env('NAGAD_MERCHANT_PRIVATE_KEY',''),
    'x_km_api_version' => env('NAGAD_X_KM_API_VERSION',''),
    'checkout_initialize_url' => env('NAGAD_CHECKOUT_INITIALIZE_URL',''),
    'checkout_complete_url' => env('NAGAD_CHECKOUT_COMPLETE_URL',''),
    'payment_verification_url' => env('NAGAD_PAYMENT_VERIFICATION_URL',''),
    'callback_url' => env('NAGAD_CALLBACK_URL', ''),
];
