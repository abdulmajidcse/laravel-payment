@extends('layouts.app')

@section('headTitle', 'New Order')

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-block d-md-flex">
                    <div class="flex-md-grow-1">
                        <h5 class="card-title">New Order</h5>
                    </div>
                    <div>
                        <a href="{{ route('nagad.index') }}" class="btn btn-sm btn-primary">Payment List</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('nagad.initPayment') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="amount" class="form-label">Payable Amount (TK)</label>
                        <input type="text" class="form-control" id="amount" name="amount"
                            value="{{ old('amount') }}" required>
                        @error('amount')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-sm btn-success">Pay with Nagad</button>
                </form>
            </div>
        </div>
    </div>
@endsection
