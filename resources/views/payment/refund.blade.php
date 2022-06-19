@extends('layouts.app')

@section('headTitle', 'Refund')

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-block d-md-flex">
                    <div class="flex-md-grow-1">
                        <h5 class="card-title">Refund</h5>
                    </div>
                    <div>
                        <a href="{{ route('payment.index') }}" class="btn btn-sm btn-primary">Payment List</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('payment.refundConfirm', $payment) }}" method="post">
                    @csrf

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Payment ID:</td>
                                    <td>{{ $payment->paymentID }}</td>
                                </tr>
                                <tr>
                                    <td>Transaction ID:</td>
                                    <td>{{ $payment->trxID }}</td>
                                </tr>

                                <tr>
                                    <td>Payment Amount (TK):</td>
                                    <td>{{ $payment->currency . ' ' . $payment->amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Refundable Amount (TK)</label>
                        <input type="text" class="form-control" id="amount" name="amount"
                            value="{{ old('amount') }}" required>
                        @error('amount')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="sku" class="form-label">SKU</label>
                        <input type="text" class="form-control" id="sku" name="sku"
                            value="{{ old('sku') }}" required>
                        @error('sku')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <input type="text" class="form-control" id="reason" name="reason"
                            value="{{ old('reason') }}" required>
                        @error('reason')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-sm btn-success">Refund with bKash</button>
                </form>
            </div>
        </div>
    </div>
@endsection
