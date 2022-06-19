<?php

use App\Http\Controllers\BkashCheckoutController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::prefix('payment')->name('payment.')->group(function() {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('new-order', [PaymentController::class, 'newOrder'])->name('newOrder');
    Route::post('create-payment', [PaymentController::class, 'createPayment'])->name('createPayment');
    Route::get('refund/{payment}', [PaymentController::class, 'refund'])->name('refund');
    Route::post('refund/{payment}', [PaymentController::class, 'refundConfirm'])->name('refundConfirm');
    Route::get('refund/{refund}/details', [PaymentController::class, 'refundDetails'])->name('refundDetails');
});

Route::prefix('bkash/checkout')->name('bkash.checkout.')->group(function() {
    Route::post('create-payment', [BkashCheckoutController::class, 'createPayment'])->name('createPayment');
    Route::post('execute-payment/{paymentId}', [BkashCheckoutController::class, 'executePayment'])->name('executePayment');
    Route::post('store-payment', [BkashCheckoutController::class, 'storePayment'])->name('storePayment');
    Route::get('callback', [BkashCheckoutController::class, 'callback'])->name('callback');
});
