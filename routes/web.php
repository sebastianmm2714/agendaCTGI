<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\SubdirectorController;
use App\Http\Controllers\ViaticosController;

use App\Http\Controllers\AprobacionController;
use App\Http\Controllers\ReportarDiaController;
use App\Http\Controllers\ViaticosPersonalController;
use App\Http\Controllers\ProfileController;

// --- RUTAS PÚBLICAS (Invitados) ---
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
            return view('welcome');
        }
        );
        Route::get('/login', [AuthenticatedSessionController::class , 'create'])->name('login');
        Route::post('/login', [AuthenticatedSessionController::class , 'store'])->middleware('throttle:5,1');
    });

// --- REDIRECCIÓN INICIAL POST-LOGIN ---
Route::get('/redirect', function () {
    return redirect()->route('inicio');
})->middleware('auth');


// --- FALLBACK DE ALMACENAMIENTO (Evita la necesidad de storage:link en producción) ---
Route::get('/storage/{path}', function ($path) {
    if (str_contains($path, '..')) {
        abort(403, 'Acceso no autorizado.');
    }
    
    $fullPath = storage_path('app/public/' . $path);
    
    if (!file_exists($fullPath) || is_dir($fullPath)) {
        abort(404);
    }
    
    return response()->file($fullPath);
})->where('path', '.*');


// --- RUTAS PROTEGIDAS (Solo usuarios logueados) ---
Route::middleware('auth')->group(function () {

    // 1. INICIO DINÁMICO
    Route::get('/inicio', [DashboardController::class , 'index'])->name('inicio');

    Route::get('/api/destinos', function () {
        return \App\Models\Departamento::with('municipios:id,nombre,departamento_id')
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get()
            ->map(function ($dep) {
                return [
                    'id'=> $dep->id,
                    'nombre' => mb_strtoupper($dep->nombre, 'UTF-8'),
                    'municipios' => $dep->municipios->map(function ($mun) {
                        return [
                            'id'=> $mun->id,
                            'nombre' => mb_strtoupper($mun->nombre, 'UTF-8'),
                            'departamento_id' => $mun->departamento_id,
                        ];
                    }),
                ];
            })->values();
    });
    Route::post('/perfil/firma', [ProfileController::class, 'updateSignature'])->name('user.signature.update')->middleware('throttle:10,1');



    // 3. FORMULARIO (Contratista)
    Route::get('/formulario/{id?}', [FormularioController::class , 'index'])->name('formulario');
    Route::post('/formulario', [FormularioController::class , 'store'])->name('formulario.store');

    // 4. PDF / LEGALIZACION
    Route::get('/ver-pdf/{id}', [FormularioController::class , 'pdf'])->name('agenda.pdf');
    Route::post('/save-pdf/agenda/{id}', [FormularioController::class, 'saveAgendaPdf'])->name('agenda.save-pdf');
    Route::get('/legalizacion/crear/{agenda}', [FormularioController::class, 'crearLegalizacion'])->name('legalizacion.crear');
    Route::post('/legalizacion/guardar/{agenda}', [FormularioController::class, 'guardarLegalizacion'])->name('legalizacion.guardar');
    Route::get('/ver-legalizacion/{agenda}', [FormularioController::class, 'verLegalizacion'])->name('legalizacion.ver');
    Route::post('/save-pdf/legalizacion/{id}', [FormularioController::class, 'saveLegalizacionPdf'])->name('legalizacion.save-pdf');
    Route::post('/legalizacion/enviar/{id}', [FormularioController::class, 'enviarLegalizacion'])->name('legalizacion.enviar');

    // 5. REPORTES (Historial General)
    Route::get('/reportes', [ReportesController::class , 'index'])->name('reportes');
    Route::get('/reportes-detalle/{agenda}', [ReportesController::class , 'show'])->name('reportes.show');
    Route::post('/reportes-detalle/{agenda}/guardar', [ReportarDiaController::class , 'store'])->name('agenda.actividad.store');
    Route::delete('/reportes/{agenda}', [ReportesController::class, 'destroy'])->name('reportes.destroy');

    // 5.1 REPORTAR DÍA
    Route::get('/reportar-dia', [ReportarDiaController::class , 'index'])->name('reportar-dia');
    Route::get('/reportar-dia/{agenda}', [ReportarDiaController::class , 'show'])->name('reportar-dia.show');
    Route::post('/reportar-dia/{agenda}/enviar', [ReportarDiaController::class , 'enviar'])->name('reportar-dia.enviar');

    // 6. RUTAS DE COORDINADOR (Supervisor Contrato)
    Route::middleware(['role:supervisor_contrato'])->group(function () {
            Route::get('/por-autorizar', [AprobacionController::class , 'index'])->name('supervisor_contrato.index');
            Route::post('/autorizar-agenda/{id}', [AprobacionController::class , 'autorizar'])->name('supervisor_contrato.autorizar');
            Route::post('/autorizar-agenda/{id}/devolver', [AprobacionController::class , 'devolver'])->name('supervisor_contrato.devolver');

            // Legalización
            Route::post('/autorizar-legalizacion/{id}', [AprobacionController::class, 'autorizarLegalizacion'])->name('supervisor_contrato.autorizar_legalizacion');
            Route::post('/devolver-legalizacion/{id}', [AprobacionController::class, 'devolverLegalizacion'])->name('supervisor_contrato.devolver_legalizacion');
        }
        );

        // 7. RUTAS DE SUBDIRECTOR (Ordenador Gasto)
        Route::middleware(['role:ordenador_gasto'])->group(function () {
            Route::get('/subdirector/bandeja', [SubdirectorController::class , 'index'])->name('ordenador_gasto.index');
            Route::post('/subdirector/firmar/{id}', [SubdirectorController::class , 'autorizar'])->name('ordenador_gasto.autorizar');
            Route::post('/subdirector/devolver/{id}', [SubdirectorController::class, 'devolver'])->name('ordenador_gasto.devolver');

            // Legalización
            Route::post('/subdirector/firmar-legalizacion/{id}', [SubdirectorController::class, 'autorizarLegalizacion'])->name('ordenador_gasto.autorizar_legalizacion');
            Route::post('/subdirector/devolver-legalizacion/{id}', [SubdirectorController::class, 'devolverLegalizacion'])->name('ordenador_gasto.devolver_legalizacion');
        }
        );

        // 8. RUTAS EXCLUSIVAS DE VIÁTICOS (Ubicadas aquí o en su middleware propio)
        Route::middleware(['role:viaticos'])->group(function () {
            // Bandeja de entrada de viáticos
            Route::get('/viaticos/bandeja', [ViaticosController::class , 'index'])->name('viaticos.index');

            // Vista de gestión (La pantalla dividida)
            Route::get('/viaticos/gestionar/{id}', [ViaticosController::class , 'gestionar'])->name('viaticos.gestionar');

            // Proceso de aprobación o devolución
            Route::post('/viaticos/procesar/{id}', [ViaticosController::class , 'procesar'])->name('viaticos.procesar');

            // Exportar a Excel (CSV)
            Route::get('/viaticos/export/{id}', [ViaticosController::class , 'export'])->name('viaticos.export');
            Route::post('/viaticos/export-bulk', [ViaticosController::class , 'exportBulk'])->name('viaticos.exportBulk');
            Route::get('/viaticos/agendas-por-supervisor', [ViaticosController::class, 'getAgendasBySupervisor'])->name('viaticos.agendasPorSupervisor');

            // --- GESTIÓN DE PERSONAL (NUEVO) ---
            Route::get('/viaticos/personal', [ViaticosPersonalController::class , 'index'])->name('viaticos.personal.index');
            Route::post('/viaticos/personal', [ViaticosPersonalController::class , 'store'])->name('viaticos.personal.store');
            Route::put('/viaticos/personal/{user}', [ViaticosPersonalController::class , 'update'])->name('viaticos.personal.update');
            Route::delete('/viaticos/personal/{user}', [ViaticosPersonalController::class , 'destroy'])->name('viaticos.personal.destroy');
            Route::get('/viaticos/personal/check-document', [ViaticosPersonalController::class , 'checkDocument'])->name('viaticos.personal.checkDocument')->middleware('throttle:30,1');
        }
        );

        // --- RUTAS DE LEGALIZACION ROLE ---
        Route::middleware(['role:legalizacion'])->group(function () {
            Route::get('/legalizacion/bandeja', [App\Http\Controllers\LegalizacionController::class, 'index'])->name('legalizacion.index');
            Route::get('/legalizacion/gestionar/{id}', [App\Http\Controllers\LegalizacionController::class, 'gestionar'])->name('legalizacion.gestionar');
            Route::post('/legalizacion/procesar/{id}', [App\Http\Controllers\LegalizacionController::class, 'procesar'])->name('legalizacion.procesar');

            // --- GESTIÓN DE PERSONAL ---
            Route::get('/legalizacion/personal', [App\Http\Controllers\LegalizacionPersonalController::class , 'index'])->name('legalizacion.personal.index');
            Route::post('/legalizacion/personal', [App\Http\Controllers\LegalizacionPersonalController::class , 'store'])->name('legalizacion.personal.store');
            Route::put('/legalizacion/personal/{user}', [App\Http\Controllers\LegalizacionPersonalController::class , 'update'])->name('legalizacion.personal.update');
            Route::delete('/legalizacion/personal/{user}', [App\Http\Controllers\LegalizacionPersonalController::class , 'destroy'])->name('legalizacion.personal.destroy');
            Route::get('/legalizacion/personal/check-document', [App\Http\Controllers\LegalizacionPersonalController::class , 'checkDocument'])->name('legalizacion.personal.checkDocument')->middleware('throttle:30,1');
        });

        // 9. RUTAS DE ADMINISTRADOR
        Route::middleware(['role:administrador'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
            
            // CRUD Catálogos
            Route::get('/catalogos', [App\Http\Controllers\Admin\AdminController::class, 'catalogos'])->name('catalogos');
            Route::resource('estados', App\Http\Controllers\Admin\EstadoAgendaController::class)->only(['store', 'update', 'destroy']);
            Route::resource('categorias', App\Http\Controllers\Admin\CategoriaPersonalController::class)->only(['store', 'update', 'destroy']);
            Route::resource('obligaciones', App\Http\Controllers\Admin\ObligacionController::class)->only(['index', 'store', 'update', 'destroy']);
            
            // CRUD Usuarios y Funcionarios
            Route::get('/usuarios/check-document', [App\Http\Controllers\Admin\UserController::class, 'checkDocument'])->name('usuarios.checkDocument')->middleware('throttle:30,1');
            Route::resource('usuarios', App\Http\Controllers\Admin\UserController::class);
            Route::get('/lideres_de_proceso/preview-pdf', [App\Http\Controllers\Admin\LiderDeProcesoController::class, 'previewPdf'])->name('lideres_de_proceso.preview_pdf');
            Route::resource('lideres_de_proceso', App\Http\Controllers\Admin\LiderDeProcesoController::class);

            // Carga Masiva
            Route::get('/carga-masiva', [App\Http\Controllers\Admin\CargaMasivaController::class, 'index'])->name('carga-masiva.index');
            Route::post('/carga-masiva/importar', [App\Http\Controllers\Admin\CargaMasivaController::class, 'importar'])->name('carga-masiva.importar')->middleware('throttle:3,1');
            Route::get('/carga-masiva/descargar-reporte', [App\Http\Controllers\Admin\CargaMasivaController::class, 'descargarReporte'])->name('carga-masiva.descargar');
            Route::get('/carga-masiva/descargar-historial/{id}', [App\Http\Controllers\Admin\CargaMasivaController::class, 'descargarReporteHistorial'])->name('carga-masiva.descargar_historial');
    });
});

Route::get('/ver-error', function () {
    $out = "";
    // 1. Mostrar estado de conexión
    try {
        \DB::connection()->getPdo();
        $out .= "Conexión a base de datos: OK\n";
    } catch (\Exception $e) {
        $out .= "Error de conexión a base de datos: " . $e->getMessage() . "\n\n";
    }

    // 2. Intentar correr migraciones en vivo y mostrar el resultado
    try {
        $out .= "Intentando ejecutar migraciones...\n";
        $exitCode = \Artisan::call('migrate', ['--force' => true]);
        $out .= "Código de salida: " . $exitCode . "\n";
        $out .= "Salida del comando:\n" . \Artisan::output() . "\n";

        // Limpiar caché de vistas para evitar plantillas desactualizadas
        $out .= "Limpiando caché de vistas...\n";
        \Artisan::call('view:clear');
        $out .= "Caché de vistas limpia.\n\n";
    } catch (\Exception $e) {
        $out .= "Error al ejecutar migraciones o limpiar caché: " . $e->getMessage() . "\n\n";
    }

    // 3. Mostrar las últimas líneas de laravel.log
    $logPath = storage_path('logs/laravel.log');
    if (file_exists($logPath)) {
        $content = file_get_contents($logPath);
        $lines = explode("\n", $content);
        $lastLines = array_slice($lines, -120);
        $out .= "\n--- ÚLTIMAS 120 LÍNEAS DE LARAVEL.LOG ---\n";
        $out .= implode("\n", $lastLines);
    } else {
        $out .= "\nNo se encontró el archivo laravel.log\n";
    }

    return response("<pre>" . htmlspecialchars($out) . "</pre>", 200, ['Content-Type' => 'text/html']);
});

require __DIR__ . '/auth.php';