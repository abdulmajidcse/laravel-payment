<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use App\Services\Bkash\BkashCheckoutTokenService;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class BkashCheckoutController extends Controller
{
    use BkashCheckoutTokenService;

    /**
     * bKash Checkout Create Payment
     */
    public function createPayment(Request $request)
    {
        try {
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
                'merchantInvoiceNumber' => uniqid('ec')
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

    /**
     * Store Payment information when successfully payment executed
     */
    public function storePayment(Request $request)
    {
        try {
            // create payment request validate
            $validator = Validator::make($request->all(), [
                'customerMsisdn' => 'required|string',
                'amount' => 'required|string',
                'merchantInvoiceNumber' => 'required|string',
                'currency' => 'required|string',
                'intent' => 'required|string',
                'paymentID' => 'required|string',
                'transactionStatus' => 'required|string',
                'trxID' => 'required|string',
                'createTime' => 'required|string',
                'updateTime' => 'required|string',
            ]);

            // return form validation error with json if error occured
            if ($validator->fails()) {
                return response()->json([
                    'errorCode' => 422,
                    'errorMessage' => "Something's wrong to store Payment in request data",
                    'errors' => $validator->getMessageBag(),
                ], 422);
            }

            $data = $validator->validated();
            $data['payWith'] = 'bKash';
            Payment::create($data);

            return response()->json(['statusCode' => 200, 'statusMessage' => 'Payment saved successfully']);
        } catch (\Throwable $th) {
            return response()->json(['errorCode' => 500, 'errorMessage' => "Something's wrong to store Payment", "serverError" => $th->getMessage()], 500);
        }
    }

    /**
     * bKash Checkout Callback
     */
    public function callback(Request $request)
    {
        if ($request->has('statusMessage')) {
            Session::flash('alertMessage', $request->get('statusMessage'));
            Session::flash('alertType', 'success');
        } elseif ($request->has('errorMessage')) {
            Session::flash('alertMessage', $request->get('errorMessage'));
            Session::flash('alertType', 'danger');
        } else {
            Session::flash('alertMessage', "Something's wrong to place your order");
            Session::flash('alertType', 'danger');
        }

        return redirect()->route('payment.index');
    }
}
