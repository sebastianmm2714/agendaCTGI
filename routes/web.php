<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AprobacionController;
use App\Http\Controllers\SubdirectorController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\AgendaActividadController;
use App\Http\Controllers\ReportarDiaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

//  Página inicial (login de Breeze)
Route::get('/', function () {
    return view('welcome');
})->middleware('guest');

//  Rutas de autenticación (Breeze)
require __DIR__ . '/auth.php';

// Vista previa PDF 
Route::get('/pdf-preview', function () {
    return view('agenda.pdf');
});

// Guardar actividad (requiere login)
Route::post('/agenda/{id}/actividad', [AgendaActividadController::class, 'store'])
    ->middleware('auth')
    ->name('agenda.actividad.store');


// Rutas protegidas
Route::middleware('auth')->group(function () {

    // 1. INICIO DINÁMICO (El que maneja los roles)
    Route::get('/inicio', [DashboardController::class, 'index'])->name('inicio');

    // ELIMINADO EL DASHBOARD DEDICADO DE INSTRUCTOR



    // Formulario (usuarios normales e instructores)
    Route::get('/formulario/{id?}', [FormularioController::class, 'index'])
        ->middleware('rol:user,contratista')
        ->name('formulario');

    Route::post('/formulario', [FormularioController::class, 'store'])
        ->middleware('rol:user,contratista')
        ->name('formulario.store');

    // PDF Agenda
    Route::get('/agenda/{id}/pdf', [FormularioController::class, 'pdf'])
        ->middleware('rol:user,contratista,supervisor_contrato,ordenador_gasto,viaticos')
        ->name('agenda.pdf');

    Route::post('/agenda/{id}/enviar', [FormularioController::class, 'enviar'])
        ->middleware('rol:user,contratista')
        ->name('agenda.enviar');

    // Reportar día
    Route::get('/reportar-dia', [ReportarDiaController::class, 'index'])
        ->middleware('rol:user,contratista')
        ->name('reportar-dia');

    Route::get('/reportar-dia/{agenda}', [ReportarDiaController::class, 'show'])
        ->middleware('rol:user,contratista')
        ->name('reportar-dia.show');

    // --- RUTAS DE APROBACIÓN ---

    // Rutas para Viaticos
    Route::middleware('rol:user,viaticos')->group(function () {
        Route::get('/viaticos/bandeja', [App\Http\Controllers\ViaticosController::class, 'index'])->name('viaticos.index');
        Route::post('/viaticos/aprobar/{id}', [App\Http\Controllers\ViaticosController::class, 'aprobar'])->name('viaticos.aprobar');
    });

    // Rutas para el Supervisor (Coordinador)
    Route::get('/por-autorizar', [AprobacionController::class, 'index'])
        ->middleware('rol:supervisor_contrato')
        ->name('coordinador.index');

    Route::post('/autorizar-agenda/{id}', [AprobacionController::class, 'autorizar'])
        ->middleware('rol:supervisor_contrato')
        ->name('agenda.autorizar');

    // Rutas para el Ordenador (Subdirector)
    Route::get('/subdirector/bandeja', [SubdirectorController::class, 'index'])
        ->middleware('rol:ordenador_gasto')
        ->name('subdirector.index');

    Route::post('/subdirector/firmar/{id}', [SubdirectorController::class, 'autorizar'])
        ->middleware('rol:ordenador_gasto')
        ->name('subdirector.autorizar');

    // --- REPORTES ---
    Route::get('/reportes', [ReportesController::class, 'index'])->name('reportes');
});


// Redireccion por rol

Route::get('/redirect', function () {
    $user = auth()->user();

    return match ($user->rol) {
        'contratista' => redirect()->route('inicio'),
        'supervisor_contrato' => redirect()->route('inicio'),
        'ordenador_gasto' => redirect()->route('inicio'),
        'viaticos' => redirect()->route('inicio'),
        'user' => redirect()->route('inicio'),
        default => abort(403),
    };
})
    ->middleware('auth')
    ->name('redirect');