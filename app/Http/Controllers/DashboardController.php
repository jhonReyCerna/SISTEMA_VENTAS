<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $total_ventas = Venta::count();
        $total_ingresos = Venta::sum('total');
        $total_clientes = Cliente::where('estado', 'ACTIVO')->count();
        $total_productos = Producto::where('estado', 'ACTIVO')->count();

        // Ventas del mes actual
        $ventas_mes = Venta::whereMonth('fecha_venta', now()->month)
                           ->whereYear('fecha_venta', now()->year)
                           ->sum('total');

        // Ventas de hoy
        $ventas_hoy = Venta::whereDate('fecha_venta', now()->toDateString())->count();

        // Productos con stock bajo
        $productos_stock_bajo = Producto::whereRaw('stock <= stock_minimo')
                                        ->where('estado', 'ACTIVO')
                                        ->count();

        // Últimas 5 ventas
        $ventas_recientes = Venta::with(['cliente', 'usuario'])
                                  ->orderBy('fecha_venta', 'desc')
                                  ->limit(5)
                                  ->get();

        // Top 5 productos más vendidos
        $productos_mas_vendidos = DB::table('detalle_ventas as dv')
            ->join('productos as p', 'dv.id_producto', '=', 'p.id_producto')
            ->select(
                'p.nombre as producto',
                DB::raw('SUM(dv.cantidad) as cantidad_vendida'),
                DB::raw('SUM(dv.subtotal) as total_generado')
            )
            ->groupBy('p.id_producto', 'p.nombre')
            ->orderBy('cantidad_vendida', 'desc')
            ->limit(5)
            ->get();

        // Top 5 clientes por compras
        $mejores_clientes = DB::table('ventas as v')
            ->join('clientes as c', 'v.id_cliente', '=', 'c.id_cliente')
            ->select(
                'c.nombre',
                DB::raw('COUNT(v.id_venta) as total_compras'),
                DB::raw('SUM(v.total) as monto_total')
            )
            ->groupBy('c.id_cliente', 'c.nombre')
            ->orderBy('monto_total', 'desc')
            ->limit(5)
            ->get();

        // Productos con stock bajo (detalles)
        $alertas_stock = Producto::whereRaw('stock <= stock_minimo')
                                 ->where('estado', 'ACTIVO')
                                 ->orderBy('stock', 'asc')
                                 ->limit(5)
                                 ->get();

        return view('dashboard', compact(
            'total_ventas',
            'total_ingresos',
            'total_clientes',
            'total_productos',
            'ventas_mes',
            'ventas_hoy',
            'productos_stock_bajo',
            'ventas_recientes',
            'productos_mas_vendidos',
            'mejores_clientes',
            'alertas_stock'
        ));
    }
}
