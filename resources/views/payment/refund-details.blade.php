@extends('layouts.app')

@section('headTitle', 'Refund Details')

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-block d-md-flex">
                    <div class="flex-md-grow-1">
                        <h5 class="card-title">Refund Details</h5>
                    </div>
                    <div>
                        <a href="{{ route('payment.index') }}" class="btn btn-sm btn-primary">Payment List</a>
                    </div>
                </div>

                @if (Session::has('alertMessage') && Session::has('alertType'))
                    <div class="mt-3 alert alert-{{ Session::get('alertType') }} alert-dismissible fade show"
                        role="alert">
                        {{ Session::get('alertMessage') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Payment ID:</td>
                                <td>{{ $refund->payment->paymentID }}</td>
                            </tr>
                            <tr>
                                <td>Original Transaction ID:</td>
                                <td>{{ $refund->payment->trxID }}</td>
                            </tr>

                            <tr>
                                <td>SKU:</td>
                                <td>{{ $refund->sku }}</td>
                            </tr>

                            <tr>
                                <td>Reason:</td>
                                <td>{{ $refund->reason }}</td>
                            </tr>

                            <tr>
                                <td>Transaction Status:</td>
                                <td>{{ $refund->transactionStatus }}</td>
                            </tr>

                            <tr>
                                <td>Refund Transaction ID:</td>
                                <td>{{ $refund->refundTrxID }}</td>
                            </tr>

                            <tr>
                                <td>Refund Amount (TK):</td>
                                <td>{{ $refund->currency . ' ' . $refund->refundAmount }}</td>
                            </tr>

                            <tr>
                                <td>Charge:</td>
                                <td>{{ $refund->charge }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
