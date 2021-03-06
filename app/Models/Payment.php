<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customerMsisdn',
        'amount',
        'merchantInvoiceNumber',
        'currency',
        'intent',
        'paymentID',
        'transactionStatus',
        'trxID',
        'payWith',
        'createTime',
        'updateTime',
    ];

    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
}
