<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('headTitle', 'Home') - {{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
</head>

<body>

    @yield('content')

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if (Session::has('alertMessage') && Session::has('alertType'))
            Swal.fire({
                text: `{{ Session::get('alertMessage') }}`,
                icon: `{{ Session::get('alertType') }}`,
            });
        @endif
    </script>

    @yield('scripts')
</body>

</html>
