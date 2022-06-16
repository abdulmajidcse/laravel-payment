<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
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
