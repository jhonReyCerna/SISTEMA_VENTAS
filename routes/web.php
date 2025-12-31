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
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'prevent.back', 'check.status'])->name('dashboard');

Route::middleware(['auth', 'prevent.back', 'check.status'])->group(function () {
    // Perfil - Todos los usuarios
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/foto', [ProfileController::class, 'updatePhoto'])->name('profile.update.photo');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Roles - Solo Admin
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // Rutas de Usuarios - Solo Admin
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('usuarios', UserController::class);
    });

    // Rutas de Clientes - Admin, Supervisor, Vendedor
    Route::middleware(['role:Admin,Supervisor,Vendedor'])->group(function () {
        Route::resource('clientes', ClienteController::class);
    });

    // Rutas de Categorías - Admin, Supervisor
    Route::middleware(['role:Admin,Supervisor'])->group(function () {
        Route::resource('categorias', CategoriaController::class);
    });

    // Rutas de Productos - Admin, Supervisor
    Route::middleware(['role:Admin,Supervisor'])->group(function () {
        Route::resource('productos', ProductoController::class);
    });

    // Rutas de Ventas - Admin, Supervisor, Vendedor
    Route::middleware(['role:Admin,Supervisor,Vendedor'])->group(function () {
        Route::resource('ventas', VentaController::class);
    });

    // Rutas de Movimientos de Stock - Admin, Supervisor
    Route::middleware(['role:Admin,Supervisor'])->group(function () {
        Route::resource('movimientos', MovimientoStockController::class)->only(['index', 'create', 'store', 'show']);
    });

    // Rutas de Reportes - Admin, Supervisor
    Route::middleware(['role:Admin,Supervisor'])->group(function () {
        Route::prefix('reportes')->name('reportes.')->group(function () {
            Route::get('/', [ReporteController::class, 'index'])->name('index');
            Route::get('/ventas-por-fecha', [ReporteController::class, 'ventasPorFecha'])->name('ventas-por-fecha');
            Route::get('/ventas-por-usuario', [ReporteController::class, 'ventasPorUsuario'])->name('ventas-por-usuario');
            Route::get('/productos-mas-vendidos', [ReporteController::class, 'productosMasVendidos'])->name('productos-mas-vendidos');
            Route::get('/stock-alertas', [ReporteController::class, 'stockYAlertas'])->name('stock-alertas');
            Route::get('/kardex', [ReporteController::class, 'kardex'])->name('kardex');
        });
    });
});

require __DIR__.'/auth.php';
