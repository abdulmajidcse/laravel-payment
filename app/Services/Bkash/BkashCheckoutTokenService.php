<?php

namespace App\Services\Bkash;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

trait BkashCheckoutTokenService
{
    /**
     * bKash Checkout Grant Token
     */
    public function checkoutGrantToken()
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => config('bkashapi.checkout.username'),
                'password' => config('bkashapi.checkout.password')
            ])->post(config('bkashapi.checkout.grant_token_url'), [
                'app_key' => config('bkashapi.checkout.app_key'),
                'app_secret' => config('bkashapi.checkout.app_secret')
            ]);

            return $response->collect();
        } catch (\Throwable $th) {
            // server error
            return Collection::make(['message' => 'Server error. Please, contact to Service Provider.']);
        }
    }

    /**
     * bKash Checkout Refresh Token
     */
    public function checkoutRefreshToken($refreshToken)
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => config('bkashapi.checkout.username'),
                'password' => config('bkashapi.checkout.password')
            ])->post(config('bkashapi.checkout.refresh_token_url'), [
                'app_key' => config('bkashapi.checkout.app_key'),
                'app_secret' => config('bkashapi.checkout.app_secret'),
                'refresh_token' => $refreshToken,
            ]);

            return $response->collect();
        } catch (\Throwable $th) {
            // server error
            return Collection::make(['message' => 'Server error. Please, contact to Service Provider.']);
        }
    }
}
