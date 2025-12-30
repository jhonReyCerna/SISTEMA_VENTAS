<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\MovimientoStock;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Display a listing of available reports.
     */
    public function index()
    {
        return view('reportes.index');
    }

    /**
     * Reporte de ventas por rango de fechas
     */
    public function ventasPorFecha(Request $request)
    {
        $fecha_inicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
        $fecha_fin = $request->get('fecha_fin', now()->toDateString());

        $ventas = DB::table('ventas as v')
            ->join('clientes as c', 'v.id_cliente', '=', 'c.id_cliente')
            ->join('usuarios as u', 'v.id_usuario', '=', 'u.id_usuario')
            ->select(
                'v.id_venta',
                'v.fecha_venta',
                'c.nombre as cliente',
                'u.nombre as vendedor',
                'v.total'
            )
            ->whereDate('v.fecha_venta', '>=', $fecha_inicio)
            ->whereDate('v.fecha_venta', '<=', $fecha_fin)
            ->orderBy('v.fecha_venta', 'desc')
            ->get();

        $total_general = $ventas->sum('total');

        return view('reportes.ventas-por-fecha', compact('ventas', 'fecha_inicio', 'fecha_fin', 'total_general'));
    }

    /**
     * Reporte de ventas por usuario (vendedor)
     */
    public function ventasPorUsuario()
    {
        $ventas_por_usuario = DB::table('ventas as v')
            ->join('usuarios as u', 'v.id_usuario', '=', 'u.id_usuario')
            ->select(
                'u.id_usuario',
                'u.nombre as vendedor',
                DB::raw('COUNT(v.id_venta) as total_ventas'),
                DB::raw('SUM(v.total) as monto_total')
            )
            ->groupBy('u.id_usuario', 'u.nombre')
            ->orderBy('monto_total', 'desc')
            ->get();

        return view('reportes.ventas-por-usuario', compact('ventas_por_usuario'));
    }

    /**
     * Reporte de productos más vendidos
     */
    public function productosMasVendidos()
    {
        $productos = DB::table('detalle_ventas as dv')
            ->join('productos as p', 'dv.id_producto', '=', 'p.id_producto')
            ->select(
                'p.id_producto',
                'p.nombre as producto',
                DB::raw('SUM(dv.cantidad) as cantidad_vendida'),
                DB::raw('SUM(dv.subtotal) as total_generado')
            )
            ->groupBy('p.id_producto', 'p.nombre')
            ->orderBy('cantidad_vendida', 'desc')
            ->get();

        return view('reportes.productos-mas-vendidos', compact('productos'));
    }

    /**
     * Reporte de stock actual y alertas
     */
    public function stockYAlertas()
    {
        $productos = DB::table('productos')
            ->select(
                'id_producto',
                'nombre',
                'stock',
                'stock_minimo',
                DB::raw("CASE 
                    WHEN stock <= stock_minimo THEN 'BAJO STOCK'
                    ELSE 'STOCK NORMAL'
                END as estado_stock")
            )
            ->where('estado', 'ACTIVO')
            ->orderBy(DB::raw('stock <= stock_minimo'), 'desc')
            ->orderBy('stock', 'asc')
            ->get();

        return view('reportes.stock-alertas', compact('productos'));
    }

    /**
     * Reporte de Kardex (movimientos de stock)
     */
    public function kardex(Request $request)
    {
        $id_producto = $request->get('id_producto');

        $query = DB::table('movimientos_stock as m')
            ->join('productos as p', 'm.id_producto', '=', 'p.id_producto')
            ->select(
                'p.id_producto',
                'p.nombre as producto',
                'm.tipo_movimiento',
                'm.cantidad',
                'm.fecha_movimiento',
                'm.motivo'
            );

        if ($id_producto) {
            $query->where('m.id_producto', $id_producto);
        }

        $movimientos = $query->orderBy('m.fecha_movimiento', 'desc')->get();

        $productos = Producto::where('estado', 'ACTIVO')->get();

        return view('reportes.kardex', compact('movimientos', 'productos', 'id_producto'));
    }
}
