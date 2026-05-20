<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Agenda CTGI - Inicio de Sesión</title>
    <link rel="icon" href="{{ asset('images/sena/logo-sena-verde.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sena-green: #39a900;
            --sena-green-dark: #2d8500;
            --sena-green-light: #e8f5e9;
            --text-main: #1a202c;
            --text-muted: #718096;
            --bg-light: #f7fafc;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            background-color: var(--bg-light);
            overflow-x: hidden;
        }

        .split-container {
            min-height: 100vh;
            display: flex;
        }

        /* Branding Section */
        .branding-section {
            background: linear-gradient(135deg, var(--sena-green) 0%, var(--sena-green-dark) 100%);
            position: relative;
            overflow: hidden;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            padding: 3rem;
        }

        .branding-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2v-4h4v-2H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.3;
        }

        .branding-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 500px;
            animation: fadeInScale 0.8s ease-out;
        }

        .branding-logo {
            background: white;
            padding: 2rem;
            border-radius: 2rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-bottom: 2.5rem;
            display: inline-block;
            transition: var(--transition);
        }

        .branding-logo:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }

        .branding-logo img {
            max-width: 220px;
            height: auto;
        }

        .branding-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .branding-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
            font-weight: 400;
        }

        /* Form Section */
        .form-section {
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            padding: 3rem;
        }

        .form-container {
            width: 100%;
            max-width: 420px;
            animation: slideInUp 0.8s ease-out;
        }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-header h1 {
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        .form-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* Input Styles */
        .form-label {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 2px solid #edf2f7;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--sena-green);
            box-shadow: 0 0 0 4px var(--sena-green-light);
            outline: none;
        }

        /* Button Styles */
        .btn-success {
            background-color: var(--sena-green);
            border-color: var(--sena-green);
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
        }

        .btn-success:hover {
            background-color: var(--sena-green-dark);
            border-color: var(--sena-green-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(57, 169, 0, 0.3);
        }

        .btn-success:active {
            transform: translateY(0);
        }

        .btn-outline-danger {
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        /* Alert Styles */
        .alert {
            border-radius: 0.75rem;
            border: none;
            padding: 1rem;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-danger {
            background-color: #fff5f5;
            color: #c53030;
        }

        /* Utility */
        .form-check-input:checked {
            background-color: var(--sena-green);
            border-color: var(--sena-green);
        }

        .form-check-label {
            font-size: 0.9rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        /* Animations */
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .split-container {
                flex-direction: column;
            }
            .branding-section {
                padding: 4rem 2rem;
                flex: none;
            }
            .branding-logo {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            .branding-logo img {
                max-width: 150px;
            }
            .branding-title {
                font-size: 1.75rem;
            }
            .form-section {
                padding: 3rem 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="split-container">
        <!-- Branding Section (Left) -->
        <div class="branding-section">
            <div class="branding-content">
                <div class="branding-logo">
                    <img src="{{ asset('images/sena/logo250.png') }}" alt="SENA Logo">
                </div>
                <h2 class="branding-title">Agenda CTGI</h2>
                <p class="branding-subtitle">
                    Plataforma institucional para la gestión eficiente de agendas de desplazamiento y trámites administrativos.
                </p>
            </div>
        </div>

        <!-- Form Section (Right) -->
        <div class="form-section">
            <div class="form-container">
                <div class="form-header">
                    <h1>Bienvenido</h1>
                    <p>Ingresa tus credenciales para acceder al sistema</p>
                </div>

                @if (Route::has('login'))
                    @auth
                        <div class="text-center">
                            <div class="alert alert-info" role="alert">
                                Ya has iniciado sesión correctamente.
                            </div>
                            <div class="d-grid gap-3">
                                <a href="{{ url('/formulario') }}" class="btn btn-success">
                                    Ir al Panel Principal
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        Cerrar sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else

                        <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate id="loginForm">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label" for="numero_documento">Número de Documento</label>
                                <input id="numero_documento" name="numero_documento" type="text" required autofocus
                                    autocomplete="username" class="form-control" placeholder="Ingrese su CC">
                                <div class="invalid-feedback">
                                    Por favor, ingrese un número de documento válido.
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Contraseña</label>
                                </div>
                                <input id="password" name="password" type="password" required
                                    autocomplete="current-password" class="form-control" placeholder="••••••••">
                                <div class="invalid-feedback">
                                    Por favor, ingrese su contraseña.
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">
                                    Recordar mi sesión
                                </label>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    Iniciar Sesión
                                </button>
                            </div>
                        </form>
                    @endauth
                @endif
                
                <div class="mt-5 text-center">
                    <p class="text-muted small mb-0">&copy; {{ date('Y') }} SENA CTGI. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Credenciales Incorrectas',
                text: 'El número de documento o la contraseña no coinciden con nuestros registros.',
                confirmButtonColor: '#39a900',
                confirmButtonText: 'Reintentar'
            });
        </script>
    @endif

    @if (session('status'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: "{{ session('status') }}",
                confirmButtonColor: '#39a900'
            });
        </script>
    @endif

    <script>
        // Lógica para validación inline de Bootstrap
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>

</html>