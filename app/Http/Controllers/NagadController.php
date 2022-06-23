<?php

namespace App\Http\Controllers;

use App\Services\Nagad\NagadService;
use Illuminate\Http\Request;

class NagadController extends Controller
{
    use NagadService;

    public function initPayment()
    {
        $paymentComplete = $this->checkoutComplete(100);
        if ($paymentComplete->has('callBackUrl')) {
            return redirect()->away($paymentComplete->get('callBackUrl'));
        }

        return $paymentComplete->get('message');
    }

    public function paymentVerify(Request $request)
    {
        return $verifyPayment = $this->paymentVerification($request->query('payment_ref_id'));
    }
}
