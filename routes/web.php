<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\MovimientoStockController;
use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Roles
    Route::resource('roles', RoleController::class);

    // Rutas de Usuarios
    Route::resource('usuarios', UserController::class);

    // Rutas de Clientes
    Route::resource('clientes', ClienteController::class);

    // Rutas de Categorías
    Route::resource('categorias', CategoriaController::class);

    // Rutas de Productos
    Route::resource('productos', ProductoController::class);

    // Rutas de Ventas
    Route::resource('ventas', VentaController::class);

    // Rutas de Movimientos de Stock
    Route::resource('movimientos', MovimientoStockController::class)->only(['index', 'create', 'store', 'show']);

    // Rutas de Reportes
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('index');
        Route::get('/ventas-por-fecha', [ReporteController::class, 'ventasPorFecha'])->name('ventas-por-fecha');
        Route::get('/ventas-por-usuario', [ReporteController::class, 'ventasPorUsuario'])->name('ventas-por-usuario');
        Route::get('/productos-mas-vendidos', [ReporteController::class, 'productosMasVendidos'])->name('productos-mas-vendidos');
        Route::get('/stock-alertas', [ReporteController::class, 'stockYAlertas'])->name('stock-alertas');
        Route::get('/kardex', [ReporteController::class, 'kardex'])->name('kardex');
    });
});

require __DIR__.'/auth.php';
