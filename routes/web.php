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
        Route::post('/login', [AuthenticatedSessionController::class , 'store']);
    });

// --- REDIRECCIÓN INICIAL POST-LOGIN ---
Route::get('/redirect', function () {
    return redirect()->route('inicio');
})->middleware('auth');


// --- RUTAS PROTEGIDAS (Solo usuarios logueados) ---
Route::middleware('auth')->group(function () {

    // 1. INICIO DINÁMICO
    Route::get('/inicio', [DashboardController::class , 'index'])->name('inicio');

    // Route::get('/api/destinos', [FormularioController::class , 'getDestinos']); // Eliminado por duplicación con api.php
    Route::post('/perfil/firma', [ProfileController::class, 'updateSignature'])->name('user.signature.update');



    // 3. FORMULARIO (Contratista)
    Route::get('/formulario/{id?}', [FormularioController::class , 'index'])->name('formulario');
    Route::post('/formulario', [FormularioController::class , 'store'])->name('formulario.store');

    // 4. PDF
    Route::get('/ver-pdf/{id}', [FormularioController::class , 'pdf'])->name('agenda.pdf');

    // 5. REPORTES (Historial General)
    Route::get('/reportes', [ReportesController::class , 'index'])->name('reportes');
    Route::get('/reportes-detalle/{agenda}', [ReportesController::class , 'show'])->name('reportes.show');
    Route::post('/reportes-detalle/{agenda}/guardar', [ReportarDiaController::class , 'store'])->name('agenda.actividad.store');

    // 5.1 REPORTAR DÍA
    Route::get('/reportar-dia', [ReportarDiaController::class , 'index'])->name('reportar-dia');
    Route::get('/reportar-dia/{agenda}', [ReportarDiaController::class , 'show'])->name('reportar-dia.show');
    Route::post('/reportar-dia/{agenda}/enviar', [ReportarDiaController::class , 'enviar'])->name('reportar-dia.enviar');

    // 6. RUTAS DE COORDINADOR (Supervisor Contrato)
    Route::middleware(['role:supervisor_contrato'])->group(function () {
            Route::get('/por-autorizar', [AprobacionController::class , 'index'])->name('supervisor_contrato.index');
            Route::post('/autorizar-agenda/{id}', [AprobacionController::class , 'autorizar'])->name('supervisor_contrato.autorizar');
            Route::post('/autorizar-agenda/{id}/devolver', [AprobacionController::class , 'devolver'])->name('supervisor_contrato.devolver');
        }
        );

        // 7. RUTAS DE SUBDIRECTOR (Ordenador Gasto)
        Route::middleware(['role:ordenador_gasto'])->group(function () {
            Route::get('/subdirector/bandeja', [SubdirectorController::class , 'index'])->name('ordenador_gasto.index');
            Route::post('/subdirector/firmar/{id}', [SubdirectorController::class , 'autorizar'])->name('ordenador_gasto.autorizar');
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

            // --- GESTIÓN DE PERSONAL (NUEVO) ---
            Route::get('/viaticos/personal', [ViaticosPersonalController::class , 'index'])->name('viaticos.personal.index');
            Route::post('/viaticos/personal', [ViaticosPersonalController::class , 'store'])->name('viaticos.personal.store');
            Route::put('/viaticos/personal/{user}', [ViaticosPersonalController::class , 'update'])->name('viaticos.personal.update');
            Route::delete('/viaticos/personal/{user}', [ViaticosPersonalController::class , 'destroy'])->name('viaticos.personal.destroy');
            Route::get('/viaticos/personal/check-document', [ViaticosPersonalController::class , 'checkDocument'])->name('viaticos.personal.checkDocument');
        }
        );

        // 9. RUTAS DE ADMINISTRADOR
        Route::middleware(['role:administrador'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
            
            // CRUD Catálogos
            Route::get('/catalogos', [App\Http\Controllers\Admin\AdminController::class, 'catalogos'])->name('catalogos');
            Route::resource('estados', App\Http\Controllers\Admin\EstadoAgendaController::class)->only(['store', 'update', 'destroy']);
            Route::resource('categorias', App\Http\Controllers\Admin\CategoriaPersonalController::class)->only(['store', 'update', 'destroy']);
            Route::resource('obligaciones', App\Http\Controllers\Admin\ObligacionController::class)->only(['index', 'store', 'update', 'destroy']);
            
            // CRUD Usuarios y Funcionarios
            Route::get('/usuarios/check-document', [App\Http\Controllers\Admin\UserController::class, 'checkDocument'])->name('usuarios.checkDocument');
            Route::resource('usuarios', App\Http\Controllers\Admin\UserController::class);
            Route::resource('funcionarios', App\Http\Controllers\Admin\FuncionarioController::class);
        });
    });

require __DIR__ . '/auth.php';