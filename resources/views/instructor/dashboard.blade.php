<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda CTGI</title>
    <link rel="icon" href="{{ asset('images/sena/logo-sena-verde.png') }}" type="image/png">
</head>
<body>
    <h1>instructor administrativo</h1>

    <div class="mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger w-100 text-start fw-semibold text-white">
                Cerrar sesion
            </button>
        </form>
    </div>
</body>
</html>