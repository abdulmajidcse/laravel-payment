<?php

namespace App\Services\Bkash;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

trait BkashCheckoutService
{
    use BkashCheckoutTokenService;

    /**
     * bKash checkout query Payment
     */
    public function checkoutQueryPayment($paymentId)
    {
        try {
            $grantToken = $this->checkoutGrantToken();
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $grantToken['id_token'],
                'X-App-Key' => config('bkashapi.checkout.app_key')
            ])->get(config('bkashapi.checkout.query_payment_url') . '/' . $paymentId);

            return $response->collect();
        } catch (\Throwable $th) {
            // server error
            return Collection::make(['errorCode' => 500, 'errorMessage' => 'Server error. Please, contact to Service Provider.']);
        }
    }

    /**
     * bKash checkout search Transaction
     */
    public function checkoutSearchTransaction($trxId)
    {
        try {
            $grantToken = $this->checkoutGrantToken();
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $grantToken['id_token'],
                'X-App-Key' => config('bkashapi.checkout.app_key')
            ])->get(config('bkashapi.checkout.search_transaction_url') . '/' . $trxId);

            return $response->collect();
        } catch (\Throwable $th) {
            // server error
            return Collection::make(['errorCode' => 500, 'errorMessage' => 'Server error. Please, contact to Service Provider.']);
        }
    }
}