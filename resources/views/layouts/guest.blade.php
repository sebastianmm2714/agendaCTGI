<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Agenda CTGI</title>
        <link rel="icon" href="{{ asset('images/sena/logo-sena-verde.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

        <style>
            body { background: #f2f5f7; }
        </style>
    </head>
    <body class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="container" style="max-width: 480px;">
            <div class="text-center mb-4">
                <a href="{{ url('/') }}" class="text-decoration-none text-success fw-semibold fs-4">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    </body>
</html>
