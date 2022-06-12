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
    ],

];