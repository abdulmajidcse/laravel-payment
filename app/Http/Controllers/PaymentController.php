<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Bkash\BkashCheckoutService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use BkashCheckoutService;

    public function index()
    {
        // for testing purpose in refund
        // return $this->checkoutQueryPayment('ZKAOUC41655374259572');
        // return $this->checkoutRefund(['paymentID' => 'ZKAOUC41655374259572', 'trxID' => '9FG5074ZTZ']);
        // return $this->checkoutRefund(['paymentID' => 'ZKAOUC41655374259572', 'trxID' => '9FG5074ZTZ', 'amount' => '34', 'sku' => 'questionbank-1', 'reason' => 'product not received']);
        $data['payments'] = Payment::latest()->get();
        return view('payment.index', $data);
    }

    public function newOrder()
    {
        return view('payment.new-order');
    }

    public function createPayment(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $data['amount'] = round($data['amount'], 2);
        $data['callbackUrl'] = config('bkashapi.checkout.callback_url');

        return view('payment.create-payment', $data);
    }
}
