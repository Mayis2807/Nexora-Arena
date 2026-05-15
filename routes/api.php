<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\AsientoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\EntradaController;
use Illuminate\Support\Facades\Route;

// RUTAS PÚBLICAS

// Autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Eventos (público)
Route::get('/eventos', [EventoController::class, 'index']);
Route::get('/eventos/{id}', [EventoController::class, 'show']);

// Asientos (público)
Route::get('/eventos/{eventoId}/asientos', [AsientoController::class, 'porEvento']);
Route::get('/eventos/{eventoId}/sectores/{sectorId}/asientos', [AsientoController::class, 'porSector']);

// RUTAS PROTEGIDAS (requieren token)

Route::middleware('auth:sanctum')->group(function () {

    // Usuario autenticado
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Reservas (carrito)
    Route::get('/reservas', [ReservaController::class, 'index']);
    Route::post('/reservas', [ReservaController::class, 'store']);
    Route::delete('/reservas/{id}', [ReservaController::class, 'destroy']);

    // Compra
    Route::post('/compra', [CompraController::class, 'store']);

    // Entradas
    Route::get('/entradas', [EntradaController::class, 'index']);
    Route::get('/entradas/{id}', [EntradaController::class, 'show']);

    // RUTAS DE ADMIN
    
    Route::middleware('admin')->prefix('admin')->group(function () {
        // Eventos
        Route::post('/eventos', [EventoController::class, 'store']);
        Route::put('/eventos/{id}', [EventoController::class, 'update']);
        Route::delete('/eventos/{id}', [EventoController::class, 'destroy']);
    
        // Sectores
        Route::post('/sectores', [SectorController::class, 'store']);
        Route::put('/sectores/{id}', [SectorController::class, 'update']);
        Route::delete('/sectores/{id}', [SectorController::class, 'destroy']);
    });

});


Route::post('/web/reservas', [App\Http\Controllers\Web\ReservaWebController::class, 'storeApi']);