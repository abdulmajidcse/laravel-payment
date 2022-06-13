@extends('layouts.app')

@section('headTitle', 'Pay with bKash')

@section('content')
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

                                console.log('create payment is success')
                                console.log(data);
                            } else {
                                console.log('create payment is null')
                                console.log(data);
                                bKash.create().onError();
                            }
                        })
                        .catch(function(error) {
                            console.log('create payment catch error')
                            console.log(error);
                            bKash.create().onError();
                        });
                },
                executeRequestOnAuthorization: function() {
                    axios.post(`{{ url('bkash/checkout/execute-payment') }}/${paymentID}`)
                        .then(function(response) {
                            data = response.data;
                            if (data && data.paymentID != null) {
                                // window.location.href = "success.html";
                                console.log('execute payment success')
                                console.log(data);
                            } else {
                                bKash.execute().onError();

                                console.log('execute payment is error')
                                console.log(data);
                            }
                        })
                        .catch(function(error) {
                            console.log('execute payment catch error')
                            console.log(error);
                            bKash.execute().onError();
                        });
                },

                onClose: function() {
                    alert('User has clicked the close button');
                }
            });
        });

        // start bKash payment dialog
        $(document).ready(function() {
            $('#bKash_button').click();
        });
    </script>
@endsection
