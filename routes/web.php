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
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\LogActividadController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'prevent.back', 'check.status', 'track.session'])->name('dashboard');

Route::middleware(['auth', 'prevent.back', 'check.status', 'stock.check', 'track.session', 'log.activity'])->group(function () {
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
        
        // Logs de Actividad - Solo Admin
        Route::get('/logs', [LogActividadController::class, 'index'])->name('logs.index');
        Route::get('/logs/{id}', [LogActividadController::class, 'show'])->name('logs.show');
        Route::get('/logs/export/csv', [LogActividadController::class, 'export'])->name('logs.export');
        Route::post('/logs/clean', [LogActividadController::class, 'clean'])->name('logs.clean');
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
        Route::get('productos/exportar/excel', [ProductoController::class, 'exportarExcel'])->name('productos.exportar-excel');
    });

    // Rutas de Ventas - Admin, Supervisor, Vendedor
    Route::middleware(['role:Admin,Supervisor,Vendedor'])->group(function () {
        Route::resource('ventas', VentaController::class);
        Route::get('ventas/{venta}/pdf', [VentaController::class, 'generarPDF'])->name('ventas.pdf');
        Route::get('ventas/{venta}/ticket', [VentaController::class, 'ticket'])->name('ventas.ticket');
        Route::get('ventas/exportar/excel', [VentaController::class, 'exportarExcel'])->name('ventas.exportar-excel');
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
            Route::get('/ventas-pdf', [ReporteController::class, 'ventasPDF'])->name('ventas-pdf');
        });
    });

    // Rutas de Notificaciones - Todos los usuarios
    Route::middleware(['role:Admin,Supervisor,Vendedor'])->group(function () {
        Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::get('/notificaciones/api', [NotificacionController::class, 'obtenerNotificaciones'])->name('notificaciones.api');
        Route::post('/notificaciones/{id}/leida', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.marcar-leida');
        Route::post('/notificaciones/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasLeidas'])->name('notificaciones.marcar-todas-leidas');
        Route::delete('/notificaciones/{id}', [NotificacionController::class, 'eliminar'])->name('notificaciones.eliminar');
        Route::get('/notificaciones/contador', [NotificacionController::class, 'contadorNoLeidas'])->name('notificaciones.contador');
    });
    // Rutas de Seguridad - Todos los usuarios autenticados
    Route::middleware(['auth'])->group(function () {
        Route::get('/security/dashboard', [SecurityController::class, 'dashboard'])->name('security.dashboard');
        
        // 2FA Routes
        Route::get('/security/2fa/setup', [SecurityController::class, 'show2FASetup'])->name('security.2fa.setup');
        Route::post('/security/2fa/enable', [SecurityController::class, 'enable2FA'])->name('security.2fa.enable');
        Route::post('/security/2fa/disable', [SecurityController::class, 'disable2FA'])->name('security.2fa.disable');
        
        // Session Management
        Route::get('/security/sessions', [SecurityController::class, 'showSessions'])->name('security.sessions');
        Route::post('/security/sessions/terminate', [SecurityController::class, 'terminateSession'])->name('security.sessions.terminate');
        Route::post('/security/sessions/terminate-all', [SecurityController::class, 'terminateAllSessions'])->name('security.sessions.terminate-all');
        
        // Login History
        Route::get('/security/login-history', [SecurityController::class, 'showLoginHistory'])->name('security.login-history');
    });

    // Password Reset Routes (no auth required)
    Route::get('/password/reset/{token}', [SecurityController::class, 'showPasswordResetForm'])->name('password.reset.form')->middleware('guest');
    Route::post('/password/reset', [SecurityController::class, 'resetPassword'])->name('password.reset.submit')->middleware('guest');

    // Rutas de Búsqueda - Todos los usuarios autenticados
    Route::middleware(['auth'])->group(function () {
        Route::get('/search/global', [SearchController::class, 'globalSearch'])->name('search.global');
        Route::get('/search/products', [SearchController::class, 'searchProducts'])->name('search.products');
        
        // Demo de rendimiento - Todos los usuarios autenticados
        Route::get('/performance/demo', function() {
            return view('performance.demo');
        })->name('performance.demo');
        
        // Admin only - Cache management
        Route::middleware(['role:Admin'])->group(function () {
            Route::post('/search/cache/clear', [SearchController::class, 'clearSearchCache'])->name('search.cache.clear');
            Route::get('/search/cache/stats', [SearchController::class, 'getCacheStats'])->name('search.cache.stats');
        });
    });
});

require __DIR__.'/auth.php';
