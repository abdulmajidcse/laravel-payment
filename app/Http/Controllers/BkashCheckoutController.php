<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use App\Services\Bkash\BkashCheckoutTokenService;
use Illuminate\Support\Facades\Validator;

class BkashCheckoutController extends Controller
{
    use BkashCheckoutTokenService;

    /**
     * bKash Checkout Create Payment
     */
    public function createPayment(Request $request)
    {
        // create payment request validate
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        // return form validation error with json if error occured
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errorMessage' => 'Error occured',
                'errors' => $validator->getMessageBag(),
            ], 422);
        }

        $data = $validator->validated();

        try {
            $grantToken = $this->checkoutGrantToken();

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $grantToken['id_token'],
                'X-App-Key' => config('bkashapi.checkout.app_key')
            ])->post(config('bkashapi.checkout.create_payment_url'), [
                'amount' => round($data['amount'], 2),
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => uniqid(Str::slug(config('app.name')) . '-')
            ]);

            return response()->json($response->collect());
        } catch (\Throwable $th) {
            // server error
            return response()->json(Collection::make(['errorMessage' => 'Server error. Please, contact to Service Provider.']), 422);
        }
    }

    /**
     * bKash Checkout Execute Payment
     */
    public function executePayment($paymentId)
    {
        try {
            $grantToken = $this->checkoutGrantToken();

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => $grantToken['id_token'],
                'X-App-Key' => config('bkashapi.checkout.app_key')
            ])->post(config('bkashapi.checkout.execute_payment_url') . '/' . $paymentId);

            return response()->json($response->collect());
        } catch (\Throwable $th) {
            // server error
            return response()->json(Collection::make(['errorMessage' => 'Server error. Please, contact to Service Provider.']), 422);
        }
    }
}
