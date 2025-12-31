<?php

namespace App\Http\Controllers;

use App\Models\CuentaPorCobrar;
use App\Models\PagoCuentaPorCobrar;
use App\Models\Cliente;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CuentaPorCobrarController extends Controller
{
    public function index(Request $request)
    {
        $query = CuentaPorCobrar::with(['cliente', 'venta']);
        
        // Filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('cliente', function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('vencidas')) {
            if ($request->vencidas === '1') {
                $query->where('fecha_vencimiento', '<', now());
            }
        }
        
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }
        
        // Ordenar
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        
        $cuentas = $query->orderBy($orderBy, $orderDir)->paginate(15);
        
        // Datos para filtros
        $clientes = Cliente::activos()->orderBy('nombre')->get();
        $estadisticas = $this->obtenerEstadisticas();
        
        return view('cuentas-por-cobrar.index', compact('cuentas', 'clientes', 'estadisticas'));
    }
    
    public function show(CuentaPorCobrar $cuentaPorCobrar)
    {
        $cuentaPorCobrar->load(['cliente', 'venta', 'pagos.metodoPago', 'pagos.registradoPor']);
        
        return view('cuentas-por-cobrar.show', compact('cuentaPorCobrar'));
    }
    
    public function pagar(CuentaPorCobrar $cuentaPorCobrar)
    {
        $metodosPago = MetodoPago::activos()->orderBy('orden')->get();
        
        return view('cuentas-por-cobrar.pagar', compact('cuentaPorCobrar', 'metodosPago'));
    }
    
    public function procesarPago(Request $request, CuentaPorCobrar $cuentaPorCobrar)
    {
        $request->validate([
            'monto_pago' => 'required|numeric|min:0.01',
            'metodo_pago_id' => 'required|exists:metodos_pago,id_metodo_pago',
            'fecha_pago' => 'required|date',
            'referencia' => 'nullable|string|max:100',
            'notas' => 'nullable|string|max:500',
            'incluir_interes' => 'boolean',
        ]);
        
        if ($request->monto_pago > $cuentaPorCobrar->monto_pendiente_total) {
            return back()->withErrors(['monto_pago' => 'El monto del pago no puede ser mayor al saldo pendiente.']);
        }
        
        DB::beginTransaction();
        
        try {
            $pago = $cuentaPorCobrar->registrarPago(
                $request->monto_pago,
                $request->metodo_pago_id,
                $request->fecha_pago,
                $request->referencia,
                $request->notas,
                $request->boolean('incluir_interes'),
                Auth::id()
            );
            
            DB::commit();
            
            return redirect()->route('cuentas-por-cobrar.show', $cuentaPorCobrar)
                           ->with('success', 'Pago registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    public function anularPago(PagoCuentaPorCobrar $pago)
    {
        DB::beginTransaction();
        
        try {
            $pago->anular(Auth::id(), 'Anulado por usuario');
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Pago anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    public function reportePorVencer(Request $request)
    {
        $dias = $request->get('dias', 30); // Próximos 30 días por defecto
        $fechaLimite = Carbon::now()->addDays($dias);
        
        $cuentas = CuentaPorCobrar::with(['cliente', 'venta'])
            ->where('estado', '!=', 'PAGADA')
            ->where('fecha_vencimiento', '<=', $fechaLimite)
            ->where('fecha_vencimiento', '>=', now())
            ->orderBy('fecha_vencimiento')
            ->get();
            
        return view('cuentas-por-cobrar.por-vencer', compact('cuentas', 'dias'));
    }
    
    public function reporteVencidas()
    {
        $cuentas = CuentaPorCobrar::with(['cliente', 'venta'])
            ->where('estado', '!=', 'PAGADA')
            ->where('fecha_vencimiento', '<', now())
            ->orderBy('fecha_vencimiento')
            ->get();
            
        // Calcular días de vencimiento
        foreach ($cuentas as $cuenta) {
            $cuenta->dias_vencida = now()->diffInDays($cuenta->fecha_vencimiento);
        }
        
        return view('cuentas-por-cobrar.vencidas', compact('cuentas'));
    }
    
    public function reporteCliente(Cliente $cliente)
    {
        $cuentas = CuentaPorCobrar::with(['venta', 'pagos'])
            ->where('cliente_id', $cliente->id_cliente)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $resumen = [
            'total_creditos' => $cuentas->count(),
            'monto_total' => $cuentas->sum('monto_original'),
            'monto_pagado' => $cuentas->sum('monto_pagado'),
            'monto_pendiente' => $cuentas->where('estado', '!=', 'PAGADA')->sum('monto_pendiente'),
            'vencidas' => $cuentas->where('estado', '!=', 'PAGADA')
                                 ->where('fecha_vencimiento', '<', now())->count(),
        ];
        
        return view('cuentas-por-cobrar.cliente', compact('cliente', 'cuentas', 'resumen'));
    }
    
    public function estadisticas()
    {
        $estadisticas = $this->obtenerEstadisticas();
        
        // Datos adicionales para gráficos
        $porMes = CuentaPorCobrar::selectRaw('MONTH(created_at) as mes, YEAR(created_at) as anio, SUM(monto_original) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('mes', 'anio')
            ->orderBy('mes')
            ->get();
            
        $porCliente = CuentaPorCobrar::with('cliente')
            ->selectRaw('cliente_id, SUM(monto_pendiente) as total_pendiente')
            ->where('estado', '!=', 'PAGADA')
            ->groupBy('cliente_id')
            ->havingRaw('total_pendiente > 0')
            ->orderBy('total_pendiente', 'desc')
            ->limit(10)
            ->get();
        
        return view('cuentas-por-cobrar.estadisticas', compact('estadisticas', 'porMes', 'porCliente'));
    }
    
    public function exportar(Request $request)
    {
        // Implementar exportación a Excel
        // Por ahora retornamos error
        return back()->with('info', 'Funcionalidad de exportación en desarrollo.');
    }
    
    private function obtenerEstadisticas()
    {
        return [
            'total_cuentas' => CuentaPorCobrar::count(),
            'monto_total' => CuentaPorCobrar::sum('monto_original'),
            'monto_pendiente' => CuentaPorCobrar::where('estado', '!=', 'PAGADA')->sum('monto_pendiente'),
            'monto_pagado' => CuentaPorCobrar::sum('monto_pagado'),
            'cuentas_vencidas' => CuentaPorCobrar::where('estado', '!=', 'PAGADA')
                                                 ->where('fecha_vencimiento', '<', now())->count(),
            'promedio_dias_pago' => $this->calcularPromedioDiasPago(),
        ];
    }
    
    private function calcularPromedioDiasPago()
    {
        $cuentasPagadas = CuentaPorCobrar::where('estado', 'PAGADA')
            ->selectRaw('DATEDIFF(fecha_ultimo_pago, created_at) as dias_pago')
            ->get();
            
        if ($cuentasPagadas->isEmpty()) {
            return 0;
        }
        
        return round($cuentasPagadas->avg('dias_pago'), 1);
    }
}