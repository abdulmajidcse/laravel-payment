<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Bkash\BkashCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Services\Bkash\BkashCheckoutTokenService;

class BkashCheckoutController extends Controller
{
    use BkashCheckoutTokenService, BkashCheckoutService;

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

            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $grantToken['id_token'],
                'X-App-Key' => config('bkashapi.checkout.app_key')
            ];

            $bodyParams = [
                'amount' => round($data['amount'], 2),
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => uniqid('ec')
            ];

            $response = Http::withHeaders($headers)->post(config('bkashapi.checkout.create_payment_url'), $bodyParams);

            // create payment message in log
            if (app()->environment('local')) {
                Log::info("\nAPI Title : Create Payment \nAPI URL: " . config('bkashapi.checkout.create_payment_url') . "\nRequest Body :");
                Log::info('headers: ', $headers);
                Log::info('body params: ', $bodyParams);
                Log::info('API Response: ', $response->collect()->toArray());
            }

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

            $headers = [
                'Accept' => 'application/json',
                'Authorization' => $grantToken['id_token'],
                'X-App-Key' => config('bkashapi.checkout.app_key')
            ];

            $response = Http::withHeaders($headers)->post(config('bkashapi.checkout.execute_payment_url') . '/' . $paymentId);

            //  Execute Payment message in log
            if (app()->environment('local')) {
                Log::info("\nAPI Title :  Execute Payment \nAPI URL: " . config('bkashapi.checkout.execute_payment_url') . '/' . $paymentId . "\nRequest Body :");
                Log::info('headers: ', $headers);
                Log::info('API Response: ', $response->collect()->toArray());
            }

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
            // query payment for check real payment
            $queryPayment = $this->checkoutQueryPayment($data['paymentID']);
            if ($queryPayment->has('trxID') && $queryPayment->get('transactionStatus') == 'Completed' && !Payment::where('trxID', $queryPayment->get('trxID'))->first()) {
                // store payment
                $formData = $queryPayment->toArray();
                $formData['customerMsisdn'] = $data['customerMsisdn'];
                $formData['payWith'] = 'bKash';
                Payment::create($formData);
                return response()->json(['statusCode' => 200, 'statusMessage' => 'Payment saved successfully']);
            } else {
                return response()->json(['errorCode' => 422, 'errorMessage' => "Payment failed! Try again."], 422);
            }
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
            Session::flash('alertMessage', $request->query('statusMessage'));
            Session::flash('alertType', 'success');
        } elseif ($request->has('errorMessage')) {
            Session::flash('alertMessage', strtolower($request->query('errorMessage')) == 'undefined' ? 'Payment failed!' : $request->query('errorMessage'));
            Session::flash('alertType', 'error');
        } else {
            Session::flash('alertMessage', "Payment failed!");
            Session::flash('alertType', 'error');
        }

        return redirect()->route('payment.index');
    }
}
