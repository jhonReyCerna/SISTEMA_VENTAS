<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\User;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Limpiar el flag de just_logged_in después de cargar el dashboard
        if (session('just_logged_in')) {
            session()->forget('just_logged_in');
        }
        
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

        // Datos para gráficos
        // Ventas por mes (últimos 12 meses)
        $ventas_por_mes = [];
        $meses_labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            $meses_labels[] = $mes->locale('es')->isoFormat('MMM YYYY');
            $ventas_por_mes[] = Venta::whereYear('fecha_venta', $mes->year)
                                     ->whereMonth('fecha_venta', $mes->month)
                                     ->sum('total');
        }

        // Ventas del año anterior (para comparativa)
        $ventas_anio_anterior = Venta::whereYear('fecha_venta', now()->subYear()->year)->sum('total');
        $ventas_anio_actual = Venta::whereYear('fecha_venta', now()->year)->sum('total');
        
        // Calcular porcentaje de crecimiento
        $crecimiento_anual = $ventas_anio_anterior > 0 
            ? (($ventas_anio_actual - $ventas_anio_anterior) / $ventas_anio_anterior) * 100 
            : 100;

        // Top 5 productos vendidos (para gráfico)
        $productos_chart_labels = [];
        $productos_chart_data = [];
        foreach ($productos_mas_vendidos as $producto) {
            $productos_chart_labels[] = $producto->producto;
            $productos_chart_data[] = $producto->cantidad_vendida;
        }

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
            'alertas_stock',
            'ventas_por_mes',
            'meses_labels',
            'ventas_anio_actual',
            'ventas_anio_anterior',
            'crecimiento_anual',
            'productos_chart_labels',
            'productos_chart_data'
        ));
    }
}
