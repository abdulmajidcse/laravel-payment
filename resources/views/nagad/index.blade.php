@extends('layouts.app')

@section('headTitle', 'Payment')

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-block d-md-flex">
                    <div class="flex-md-grow-1">
                        <h5 class="card-title">Payment List (Nagad)</h5>
                    </div>
                    <div>
                        <a href="{{ route('nagad.newOrder') }}" class="btn btn-sm btn-primary">New Order</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Order ID</th>
                                <th scope="col">Client Mobile No</th>
                                <th scope="col">Trx ID</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($nagadPayments as $nagadPayment)
                                <tr>
                                    <td>{{ ++$loop->index }}</td>
                                    <td>{{ $nagadPayment->orderId }}</td>
                                    <td>{{ $nagadPayment->clientMobileNo }}</td>
                                    <td>{{ $nagadPayment->issuerPaymentRefNo }}</td>
                                    <td>BDT {{ $nagadPayment->amount }}</td>
                                    <td>{{ $nagadPayment->status }}</td>
                                    <td>{{ $nagadPayment->issuerPaymentDateTime->format('Y-m-d h:i A') }}</td>
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
