@extends('layouts.app')

@section('headTitle', 'Pay with bKash')

@section('content')
    <div class="d-flex justify-content-center align-items-center" style="height: 100vh; width: 100%;">
        <button class="btn btn-danger" type="button" disabled>
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Please wait...
        </button>
    </div>

    <button id="bKash_button" class="d-none">Pay with bKash</button>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/axios.min.js') }}"></script>
    <script src="{{ config('bkashapi.checkout.script_url') }}"></script>
    <script>
        $(function() {
            let paymentID = '';

            bKash.init({
                paymentMode: 'checkout',
                paymentRequest: {
                    amount: `{{ $amount }}`,
                    intent: 'sale',
                    currency: 'BDT'
                },
                createRequest: function(request) {
                    axios.post(`{{ route('bkash.checkout.createPayment') }}`, request)
                        .then(function(response) {
                            data = response.data;
                            if (data && data.paymentID != null) {
                                paymentID = data.paymentID;
                                bKash.create().onSuccess(data);
                            } else {
                                // error 
                                window.location.href =
                                    `{{ $callbackUrl }}?errorMessage=${data.errorMessage}`;
                                // send bKash error
                                bKash.create().onError();
                            }
                        })
                        .catch(function(error) {
                            // error 
                            window.location.href =
                                `{{ $callbackUrl }}?errorMessage=${error.response?.data?.errorMessage ?? error.message}`;
                            // send bKash error
                            bKash.create().onError();
                        });
                },
                executeRequestOnAuthorization: function() {
                    axios.post(`{{ url('bkash/checkout/execute-payment') }}/${paymentID}`)
                        .then(function(response) {
                            data = response.data;
                            if (data && data.paymentID != null && data.transactionStatus ===
                                'Completed') {
                                // payment success
                                axios.post(`{{ route('bkash.checkout.storePayment') }}`, data)
                                .then(function(response) {
                                    window.location.href = `{{ $callbackUrl }}?statusMessage=Your order placed successfully`;
                                })
                                .catch(function(error) {
                                    window.location.href = `{{ $callbackUrl }}?errorMessage=${error.response?.data?.errorMessage ?? error.message}`;
                                });
                            } else {
                                // send bKash error
                                bKash.execute().onError();

                                // error
                                window.location.href =
                                    `{{ $callbackUrl }}?errorMessage=${data.errorMessage}`;
                            }
                        })
                        .catch(function(error) {
                            // error
                            window.location.href =
                                `{{ $callbackUrl }}?errorMessage=${error.response?.data?.errorMessage ?? error.message}`;

                            // send bKash error
                            bKash.execute().onError();
                        });
                },

                onClose: function() {
                    // user close payment dialog
                    window.location.href =
                        `{{ $callbackUrl }}?errorMessage=You are Canceled bKash Payment Dialog`;
                }
            });
        });

        // start bKash payment dialog
        $(document).ready(function() {
            $('#bKash_button').click();
        });
    </script>
@endsection
