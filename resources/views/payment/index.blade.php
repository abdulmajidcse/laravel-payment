@extends('layouts.app')

@section('headTitle', 'Payment')

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-block d-md-flex">
                    <div class="flex-md-grow-1">
                        <h5 class="card-title">Payment List</h5>
                    </div>
                    <div>
                        <a href="{{ route('payment.newOrder') }}" class="btn btn-sm btn-primary">New Order</a>
                    </div>
                </div>

                @if (Session::has('alertMessage') && Session::has('alertType'))
                    <div class="mt-3 alert alert-{{ Session::get('alertType') }} alert-dismissible fade show" role="alert">
                        {{ Session::get('alertMessage') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Customer Msisdn</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Merchant Invoice Number</th>
                                <th scope="col">Payment ID</th>
                                <th scope="col">Trx ID</th>
                                <th scope="col">transactionStatus</th>
                                <th scope="col">Pay With</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr>
                                    <td>{{ ++$loop->index }}</td>
                                    <td>{{ $payment->customerMsisdn }}</td>
                                    <td>{{ $payment->currency . ' ' . $payment->amount }}</td>
                                    <td>{{ $payment->merchantInvoiceNumber }}</td>
                                    <td>{{ $payment->paymentID }}</td>
                                    <td>{{ $payment->trxID }}</td>
                                    <td>{{ $payment->transactionStatus }}</td>
                                    <td>{{ $payment->payWith }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-danger text-center">No data available!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
