<?php

namespace App\Http\Controllers;

use App\Services\Bkash\BkashCheckoutTokenService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use BkashCheckoutTokenService;

    public function index()
    {
        // try {
        //     $grantToken = $this->checkoutGrantToken();

        //     // return $grantToken;

        //     if ($grantToken && $grantToken->has('id_token')) {
        //         return $grantToken['id_token'];
        //     } else {
        //         return $grantToken->has('message') ? $grantToken['message'] : $grantToken['msg'];
        //     }


        // } catch (\Throwable $th) {
        //     return $th->getMessage();
        // }

        return view('payment.index');
    }
}
