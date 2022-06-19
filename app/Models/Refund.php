<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_id',
        'sku',
        'reason',
        'transactionStatus',
        'refundTrxID',
        'refundAmount',
        'currency',
        'charge',
        'completedTime'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
