<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Refund;
use App\Services\Bkash\BkashCheckoutService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use BkashCheckoutService;

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

    public function refund(Payment $payment)
    {
        $data['payment'] = $payment;

        return view('payment.refund', $data);
    }

    public function refundConfirm(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $payment->amount,
            'sku' => 'required|string',
            'reason' => 'required|string',
        ]);

        $data['amount'] = round($data['amount'], 2);
        $data['paymentID'] = $payment->paymentID;
        $data['trxID'] = $payment->trxID;

        $refundResponse = $this->checkoutRefund($data);

        $refund = Refund::create([
            'payment_id' => $payment->id,
            'sku' => $data['sku'],
            'reason' => $data['reason'],
            'transactionStatus' => $refundResponse['transactionStatus'],
            'refundTrxID' => $refundResponse['refundTrxID'],
            'refundAmount' => $refundResponse['amount'],
            'currency' => $refundResponse['currency'],
            'charge' => $refundResponse['charge'],
            'completedTime' => $refundResponse['completedTime'],
        ]);

        return redirect()->route('payment.refundDetails', $refund->id)->with(['alertMessage' => 'Successfully refund', 'alertType' => 'success']);
    }

    public function refundDetails(Refund $refund)
    {
        $data['refund'] = $refund;

        return view('payment.refund-details', $data);
    }

    public function trxDetails($trxId)
    {
        return $this->checkoutSearchTransaction($trxId);
    }
}
