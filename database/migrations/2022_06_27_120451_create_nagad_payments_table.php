<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNagadPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nagad_payments', function (Blueprint $table) {
            $table->id();
            $table->string('merchantId');
            $table->string('orderId');
            $table->text('paymentRefId');
            $table->string('amount');
            $table->string('clientMobileNo')->nullable();
            $table->string('merchantMobileNo')->nullable();
            $table->string('orderDateTime')->nullable();
            $table->string('issuerPaymentDateTime')->nullable();
            $table->string('issuerPaymentRefNo');
            $table->text('additionalMerchantInfo')->nullable();
            $table->string('status');
            $table->string('statusCode');
            $table->string('cancelIssuerDateTime')->nullable();
            $table->string('cancelIssuerRefNo')->nullable();
            $table->string('serviceType')->nullable();
            $table->string('currencyCode')->default('050');
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
        Schema::dropIfExists('nagad_payments');
    }
}
