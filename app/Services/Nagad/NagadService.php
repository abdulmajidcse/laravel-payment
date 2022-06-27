<?php

namespace App\Services\Nagad;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

trait NagadService
{
    /**
     * Encoded Sensitive Data
     */
    private function encodedSensitiveData(array $data)
    {
        // encrypted encoded sensitive data
        $public_key = "-----BEGIN PUBLIC KEY-----\n" . config('nagadapi.pg_public_key') . "\n-----END PUBLIC KEY-----";
        $sslPublicKey = openssl_get_publickey($public_key);
        openssl_public_encrypt(json_encode($data), $crypttext, $sslPublicKey);
        return base64_encode($crypttext);
    }

    /**
     * Encoded Signature
     */
    private function encodedSignature(array $data)
    {
        // encrypted encoded signature
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . config('nagadapi.merchant_private_key') . "\n-----END RSA PRIVATE KEY-----";
        openssl_sign(json_encode($data), $sign, $private_key, OPENSSL_ALGO_SHA256);
        return base64_encode($sign);
    }

    /**
     * Data Decrypt with Merchant Private Key
     */
    private function decryptDataWithPrivateKey($crypttext)
    {
        $merchanPrivateKey = "-----BEGIN RSA PRIVATE KEY-----\n" . config('nagadapi.merchant_private_key') . "\n-----END RSA PRIVATE KEY-----";
        openssl_private_decrypt(base64_decode($crypttext), $plainText, $merchanPrivateKey);
        return json_decode($plainText);
    }

    /**
     * Nagad Checkout Initialize API
     */
    private function checkoutInitialize()
    {
        try {
            $data = [
                'merchantId' => config('nagadapi.merchant_id'),
                'datetime' => date("YmdHis"),
                'orderId' => uniqid(),
                'challenge' => Str::random()
            ];

            $sensitiveData = $this->encodedSensitiveData($data);
            $signature = $this->encodedSignature($data);

            $response = Http::withHeaders([
                'X-KM-IP-V4' => request()->ip(),
                'X-KM-Client-Type' => 'PC_WEB',
                'X-KM-Api-Version' => config('nagadapi.x_km_api_version'),
                'Content-Type' => 'application/json'
            ])->post(config('nagadapi.checkout_initialize_url') . '/' . $data['merchantId'] . '/' . $data['orderId'], [
                "accountNumber" => config('nagadapi.merchant_number'),
                "dateTime" => $data['datetime'],
                "sensitiveData" => $sensitiveData,
                "signature" => $signature
            ]);

            $responseCollection = $response->collect();

            foreach ($data as $key => $value) {
                $responseCollection->put($key, $value);
            }

            return $responseCollection;
        } catch (\Throwable $th) {
            return $th;
            // server error
            return Collection::make(['reason' => 500, 'message' => 'Server error. Please, contact to Service Provider.']);
        }
    }

    /**
     * Nagad Checkout Complete API
     */
    public function checkoutComplete(float $amount, array $additionalMerchantInfo = [])
    {
        try {
            $initializeResponse = $this->checkoutInitialize();

            $paymentReferenceResponse = Collection::make($this->decryptDataWithPrivateKey($initializeResponse['sensitiveData']));

            $data = [
                'merchantId' => $initializeResponse['merchantId'],
                'amount' => round($amount, 2),
                'currencyCode' => '050',
                'challenge' => $paymentReferenceResponse['challenge'],
                'orderId' => $initializeResponse['orderId'],
            ];

            $requestData = [
                "sensitiveData" => $this->encodedSensitiveData($data),
                "signature" => $this->encodedSignature($data),
                'merchantCallbackURL' => config('nagadapi.callback_url')
            ];

            if (count($additionalMerchantInfo) > 0) {
                $requestData['additionalMerchantInfo'] = $additionalMerchantInfo;
            }

            $response = Http::withHeaders([
                'X-KM-IP-V4' => request()->ip(),
                'X-KM-Client-Type' => 'PC_WEB',
                'X-KM-Api-Version' => config('nagadapi.x_km_api_version'),
                'Content-Type' => 'application/json'
            ])->post(config('nagadapi.checkout_complete_url') . '/' . $paymentReferenceResponse['paymentReferenceId'], $requestData);

            return $response->collect();
        } catch (\Throwable $th) {
            // server error
            return Collection::make(['reason' => 500, 'message' => 'Server error. Please, contact to Service Provider.']);
        }
    }

    /**
     * 
     */
    public function paymentVerification($paymentReferenceId)
    {
        try {
            $response = Http::withHeaders([
                'X-KM-IP-V4' => request()->ip(),
                'X-KM-Client-Type' => 'PC_WEB',
                'X-KM-Api-Version' => config('nagadapi.x_km_api_version'),
                'Content-Type' => 'application/json'
            ])->get(config('nagadapi.payment_verification_url') . '/' . $paymentReferenceId);

            return $response->collect();
        } catch (\Throwable $th) {
            // server error
            return Collection::make(['reason' => 500, 'message' => 'Server error. Please, contact to Service Provider.']);
        }
    }
}
