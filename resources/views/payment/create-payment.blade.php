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
                                    `{{ route('bkash.checkout.callback') }}?errorMessage=${data.errorMessage}`;
                                // send bKash error
                                bKash.create().onError();
                            }
                        })
                        .catch(function(error) {
                            // error 
                            window.location.href =
                                `{{ route('bkash.checkout.callback') }}?errorMessage=${error.errorMessage}`;
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
                                window.location.href = `{{ route('bkash.checkout.callback') }}?trxID=${data.trxID}`;
                            } else {
                                // send bKash error
                                bKash.execute().onError();

                                // error
                                window.location.href =
                                    `{{ route('bkash.checkout.callback') }}?errorMessage=${data.errorMessage}`;
                            }
                        })
                        .catch(function(error) {
                            // error
                            window.location.href =
                                `{{ route('bkash.checkout.callback') }}?errorMessage=${error.errorMessage}`;

                            // send bKash error
                            bKash.execute().onError();
                        });
                },

                onClose: function() {
                    // user close payment dialog
                    window.location.href =
                        `{{ route('bkash.checkout.callback') }}?errorMessage=User Cancel Payment Dialog`;
                }
            });
        });

        // start bKash payment dialog
        $(document).ready(function() {
            $('#bKash_button').click();
        });
    </script>
@endsection
