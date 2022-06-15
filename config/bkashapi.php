<?php

return [

    'checkout' => [
        'app_key' => env('BKASH_CHECKOUT_APP_KEY', ''),
        'app_secret' => env('BKASH_CHECKOUT_APP_SECRET', ''),
        'username' => env('BKASH_CHECKOUT_USERNAME', ''),
        'password' => env('BKASH_CHECKOUT_PASSWORD', ''),
        'callback_url' => env('BKASH_CHECKOUT_CALLBACK_URL', ''),
        'grant_token_url' => env('BKASH_CHECKOUT_GRANT_TOKEN_URL', ''),
        'refresh_token_url' => env('BKASH_CHECKOUT_REFRESH_TOKEN_URL', ''),
        'script_url' => env('BKASH_CHECKOUT_SCRIPT_URL', ''),
        'script_url' => env('BKASH_CHECKOUT_SCRIPT_URL', ''),
        'create_payment_url' => env('BKASH_CHECKOUT_CREATE_PAYMENT_URL', ''),
        'execute_payment_url' => env('BKASH_CHECKOUT_EXECUTE_PAYMENT_URL', ''),

        // cache name
        'cache_grant_token_name' => 'bkash_checkout_grant_token',
        'cache_refresh_token_name' => 'bkash_checkout_refresh_token',
    ],

];
