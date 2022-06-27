<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NagadPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'merchantId',
        'orderId',
        'paymentRefId',
        'amount',
        'clientMobileNo',
        'merchantMobileNo',
        'orderDateTime',
        'issuerPaymentDateTime',
        'issuerPaymentRefNo',
        'additionalMerchantInfo',
        'status',
        'statusCode',
        'cancelIssuerDateTime',
        'cancelIssuerRefNo',
        'serviceType',
        'currencyCode',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'issuerPaymentDateTime' => 'datetime:Y-m-d h:i A',
    ];
}
