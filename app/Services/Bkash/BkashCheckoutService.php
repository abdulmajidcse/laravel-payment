<?php

namespace App\Services\Bkash;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $grantToken['id_token'],
                'X-App-Key' => config('bkashapi.checkout.app_key')
            ];

            $response = Http::withHeaders($headers)->get(config('bkashapi.checkout.query_payment_url') . '/' . $paymentId);

            // Query Payment message in log
            Log::info("\nAPI Title : Query Payment \nAPI URL: " . config('bkashapi.checkout.query_payment_url') . '/' . $paymentId . "\nRequest Body :");
            Log::info('headers: ', $headers);
            Log::info('API Response: ', $response->collect()->toArray());

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

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $grantToken['id_token'],
                'X-App-Key' => config('bkashapi.checkout.app_key')
            ];

            $response = Http::withHeaders($headers)->get(config('bkashapi.checkout.search_transaction_url') . '/' . $trxId);

            // : Search Transaction Details message in log
            Log::info("\nAPI Title : : Search Transaction Details \nAPI URL: " . config('bkashapi.checkout.search_transaction_url') . '/' . $trxId . "\nRequest Body :");
            Log::info('headers: ', $headers);
            Log::info('API Response: ', $response->collect()->toArray());

            return $response->collect();
        } catch (\Throwable $th) {
            // server error
            return Collection::make(['errorCode' => 500, 'errorMessage' => 'Server error. Please, contact to Service Provider.']);
        }
    }

    /**
     * bKash checkout refund
     * 
     * if a payment is refunded and can see details put 2 parameters (paymentID, trxID)
     * 
     * otherwise, 5 parameters (paymentID, trxID, amount, sku, reason)
     */
    public function checkoutRefund(array $refundInfo)
    {
        try {
            $grantToken = $this->checkoutGrantToken();
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => $grantToken['id_token'],
                'X-App-Key' => config('bkashapi.checkout.app_key')
            ])->post(config('bkashapi.checkout.refund_url'), $refundInfo);

            return $response->collect();
        } catch (\Throwable $th) {
            // server error
            return Collection::make(['errorCode' => 500, 'errorMessage' => 'Server error. Please, contact to Service Provider.']);
        }
    }
}
