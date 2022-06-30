<?php

namespace App\Services\Bkash;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait BkashCheckoutTokenService
{
    /**
     * bKash Checkout Grant Token
     */
    public function checkoutGrantToken()
    {
        try {
            $cacheGrantToken = config('bkashapi.checkout.cache_grant_token_name');
            $cacheRefreshToken = config('bkashapi.checkout.cache_refresh_token_name');
            if (Cache::has($cacheGrantToken)) {
                return Cache::get($cacheGrantToken);
            } elseif (Cache::has($cacheRefreshToken)) {
                return $this->checkoutRefreshToken(Cache::get($cacheRefreshToken)['refresh_token']);
            }

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => config('bkashapi.checkout.username'),
                'password' => config('bkashapi.checkout.password')
            ];

            $bodyParams = [
                'app_key' => config('bkashapi.checkout.app_key'),
                'app_secret' => config('bkashapi.checkout.app_secret')
            ];

            $response = Http::withHeaders($headers)->post(config('bkashapi.checkout.grant_token_url'), $bodyParams);

            $responseCollection = $response->collect();

            // cached grant & refresh token
            if ($responseCollection->has('id_token')) {
                $this->checkoutTokenCached($responseCollection);
            }

            // grant token message in log
            Log::info("\nAPI Title : Grant Token \nAPI URL: " . config('bkashapi.checkout.grant_token_url') . "\nRequest Body :");
            Log::info('headers: ', $headers);
            Log::info('body params: ', $bodyParams);
            Log::info('API Response: ', $responseCollection->toArray());

            return $responseCollection;
        } catch (\Throwable $th) {
            // server error
            return Collection::make(['errorCode' => 500, 'errorMessage' => 'Server error. Please, contact to Service Provider.']);
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

            $responseCollection = $response->collect();

            // cached grant & refresh token
            if ($responseCollection->has('id_token')) {
                $this->checkoutTokenCached($responseCollection);
            }

            return $responseCollection;
        } catch (\Throwable $th) {
            // server error
            return Collection::make(['errorCode' => 500, 'errorMessage' => 'Server error. Please, contact to Service Provider.']);
        }
    }

    /**
     * Store Grant Token & Refresh Token in Cache
     */
    private function checkoutTokenCached($token)
    {
        // grant token store in cache for 50 minutes
        Cache::put(config('bkashapi.checkout.cache_grant_token_name'), $token, now()->addMinutes(50));

        // refresh token store in cache for 55 minutes
        Cache::put(config('bkashapi.checkout.cache_refresh_token_name'), $token, now()->addMinutes(55));

        return true;
    }
}
