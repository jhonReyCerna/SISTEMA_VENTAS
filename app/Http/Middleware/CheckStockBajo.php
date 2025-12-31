<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Producto;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStockBajo
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo ejecutar para usuarios autenticados
        if (Auth::check()) {
            // Verificar productos con stock bajo
            $productosStockBajo = Producto::where('estado', 'ACTIVO')
                                         ->whereRaw('stock <= stock_minimo')
                                         ->get();

            foreach ($productosStockBajo as $producto) {
                // Verificar si ya existe una notificación reciente para este producto
                $notificacionExistente = Notificacion::where('tipo', 'stock_bajo')
                                                    ->where('url', 'LIKE', '%productos/' . $producto->id_producto . '%')
                                                    ->where('fecha_creacion', '>=', now()->subHours(24))
                                                    ->exists();

                // Si no existe notificación reciente, crear una nueva
                if (!$notificacionExistente) {
                    Notificacion::crearAlertaStockBajo($producto);
                }
            }
        }

        return $next($request);
    }
}