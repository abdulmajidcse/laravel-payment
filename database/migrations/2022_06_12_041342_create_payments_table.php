<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('customerMsisdn');
            $table->double('amount');
            $table->string('merchantInvoiceNumber');
            $table->string('currency');
            $table->string('intent');
            $table->string('paymentID');
            $table->string('transactionStatus');
            $table->string('trxID');
            $table->string('payWith');
            $table->timestampTz('createTime');
            $table->timestampTz('updateTime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
