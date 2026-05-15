<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\EventoWebController;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\EntradaWebController;
use App\Http\Controllers\Web\ReservaWebController;
use App\Http\Controllers\Web\CompraWebController;
use App\Http\Controllers\Web\AdminController;


// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Eventos
Route::get('/eventos', [EventoWebController::class, 'index'])->name('eventos.index');
Route::get('/eventos/{id}', [EventoWebController::class, 'show'])->name('eventos.show');

// Auth
Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register']);
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/mis-entradas', [EntradaWebController::class, 'index'])->name('entradas.index');
    Route::get('/mis-entradas/{id}', [EntradaWebController::class, 'show'])->name('entradas.show');
    
    // Reservas
    Route::post('/reservas', [ReservaWebController::class, 'store'])->name('reservas.store');
    Route::delete('/reservas/{id}', [ReservaWebController::class, 'destroy'])->name('reservas.destroy');
    Route::get('/carrito', [ReservaWebController::class, 'index'])->name('carrito.index');

    // Compra
    Route::post('/compra', [CompraWebController::class, 'store'])->name('compra.store');
    Route::get('/compra/confirmacion', [CompraWebController::class, 'confirmacion'])->name('compra.confirmacion');
});


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    
    // Eventos
    Route::get('/eventos', [AdminController::class, 'eventos'])->name('eventos');
    Route::get('/eventos/crear', [AdminController::class, 'crearEvento'])->name('eventos.crear');
    Route::post('/eventos', [AdminController::class, 'storeEvento'])->name('eventos.store');
    Route::get('/eventos/{id}/editar', [AdminController::class, 'editarEvento'])->name('eventos.editar');
    Route::put('/eventos/{id}', [AdminController::class, 'updateEvento'])->name('eventos.update');
    Route::delete('/eventos/{id}', [AdminController::class, 'destroyEvento'])->name('eventos.destroy');

    // Sectores
    Route::get('/sectores', [AdminController::class, 'sectores'])->name('sectores');
    Route::post('/sectores', [AdminController::class, 'storeSector'])->name('sectores.store');
    Route::put('/sectores/{id}', [AdminController::class, 'updateSector'])->name('sectores.update');
    Route::delete('/sectores/{id}', [AdminController::class, 'destroySector'])->name('sectores.destroy');

    // Usuarios
    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios');
});