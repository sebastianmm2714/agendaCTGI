<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Agenda CTGI</title>
    <link rel="icon" href="{{ asset('images/sena/logo-sena-verde.png') }}" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid min-vh-100">
        <div class="row min-vh-100 g-0">
            <div class="col-12 col-lg-6 bg-light d-flex align-items-center justify-content-center">
                <div class="text-center p-4 p-lg-5">
                    <img src="{{ asset('images/sena/logo250.png') }}" alt="SENA" class="img-fluid w-75 mb-3">
                    <h2 class="h4 fw-bold mb-2 text-success">Agenda CTGI</h2>
                    <p class="text-muted mb-0">
                        Gestiona tus agendas de desplazamiento de manera eficiente.
                    </p>
                </div>
            </div>
            <div class="col-12 col-lg-6 bg-white d-flex align-items-center py-5">
                <div class="w-100">
                    <div class="row justify-content-center">
                        <div class="col-11 col-sm-9 col-md-8 col-lg-9 col-xl-7">
                            <h1 class="h4 fw-bold mb-3">Iniciar sesion</h1>

                            @if (Route::has('login'))
                                @auth
                                    <div class="d-grid gap-2">
                                        <a href="{{ url('/formulario') }}" class="btn btn-success">
                                            Ir al formulario
                                        </a>

                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger">
                                                Cerrar sesión
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    @if ($errors->has('email'))
                                        <div class="alert alert-danger mb-3" role="alert">
                                            Credenciales incorrectas. Verifica tu correo y contrasena.
                                        </div>
                                    @endif
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" for="email">Correo</label>
                                            <input id="email" name="email" type="email" required autofocus
                                                autocomplete="username" class="form-control" placeholder="correo@ejemplo.com">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" for="password">Contrasena</label>
                                            <input id="password" name="password" type="password" required
                                                autocomplete="current-password" class="form-control" placeholder="********">
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                            <label class="form-check-label" for="remember">Recordarme</label>
                                        </div>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-success">Ingresar</button>
                                        </div>
                                    </form>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>