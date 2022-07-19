<?php

namespace App\Http\Controllers;

use App\Models\NagadPayment;
use App\Services\Nagad\NagadService;
use Illuminate\Http\Request;

class NagadController extends Controller
{
    use NagadService;

    public function index()
    {
        $data['nagadPayments'] = NagadPayment::latest()->get();
        return view('nagad.index', $data);
    }

    public function newOrder()
    {
        return view('nagad.new-order');
    }

    public function initPayment(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $data['amount'] = round($data['amount'], 2);

        $paymentComplete = $this->checkoutComplete($data['amount']);
        if ($paymentComplete->has('callBackUrl')) {
            return redirect()->away($paymentComplete->get('callBackUrl'));
        }

        return $paymentComplete->get('message');
    }

    public function paymentVerify(Request $request)
    {
        $verifyPayment = $this->paymentVerification($request->query('payment_ref_id'));

        if ($verifyPayment->get('status') == 'Success' && !is_null($verifyPayment->get('issuerPaymentRefNo'))) {
            $isPaymentExist = NagadPayment::where('paymentRefId', $verifyPayment->get('paymentRefId'))->first();

            if ($isPaymentExist) {
                $request->session()->flash('alertMessage', 'Payment is already checkout. Please, try another!');
                $request->session()->flash('alertType', 'warning');
            } else {
                NagadPayment::create($verifyPayment->toArray());
                $request->session()->flash('alertMessage', 'Your order placed successfully');
                $request->session()->flash('alertType', 'success');
            }
        } else {
            // for any error if present
            $request->session()->flash('alertMessage', 'Something went wrong to payment. Please, try again!');
            $request->session()->flash('alertType', 'error');
        }

        return redirect()->route('nagad.index');
    }
}
