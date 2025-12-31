<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\DetalleDevolucion;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\MovimientoStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    public function index(Request $request)
    {
        $query = Devolucion::with(['venta.cliente', 'procesadoPor']);
        
        // Filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_devolucion', 'like', "%{$search}%")
                  ->orWhereHas('venta', function($subq) use ($search) {
                      $subq->where('numero_venta', 'like', "%{$search}%");
                  })
                  ->orWhereHas('venta.cliente', function($subq) use ($search) {
                      $subq->where('nombre', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('motivo')) {
            $query->where('motivo_devolucion', $request->motivo);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_devolucion', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_devolucion', '<=', $request->fecha_hasta);
        }
        
        $devoluciones = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('devoluciones.index', compact('devoluciones'));
    }
    
    public function create()
    {
        return view('devoluciones.create');
    }
    
    public function buscarVenta(Request $request)
    {
        $request->validate([
            'numero_venta' => 'required|string',
        ]);
        
        $venta = Venta::with(['cliente', 'detalles.producto'])
            ->where('numero_venta', $request->numero_venta)
            ->first();
        
        if (!$venta) {
            return response()->json(['error' => 'Venta no encontrada'], 404);
        }
        
        // Verificar si la venta ya tiene devolución completa
        $devolucionExistente = Devolucion::where('venta_id', $venta->id_venta)
            ->where('estado', '!=', 'CANCELADA')
            ->first();
            
        $productosYaDevueltos = [];
        if ($devolucionExistente) {
            $productosYaDevueltos = $devolucionExistente->detalles()
                ->selectRaw('producto_id, SUM(cantidad_devuelta) as total_devuelto')
                ->groupBy('producto_id')
                ->get()
                ->keyBy('producto_id');
        }
        
        // Calcular cantidades disponibles para devolución
        foreach ($venta->detalles as $detalle) {
            $yaDevuelto = isset($productosYaDevueltos[$detalle->producto_id]) 
                ? $productosYaDevueltos[$detalle->producto_id]->total_devuelto 
                : 0;
            $detalle->cantidad_disponible = $detalle->cantidad - $yaDevuelto;
        }
        
        return response()->json([
            'venta' => $venta,
            'tiene_devolucion_previa' => $devolucionExistente ? true : false,
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'venta_id' => 'required|exists:ventas,id_venta',
            'motivo_devolucion' => 'required|in:DEFECTO_PRODUCTO,CLIENTE_INSATISFECHO,ERROR_VENDEDOR,GARANTIA,OTRO',
            'observaciones' => 'nullable|string|max:1000',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.condicion' => 'required|in:NUEVO,USADO_BUENO,USADO_REGULAR,DAÑADO',
            'productos.*.retornar_inventario' => 'boolean',
            'productos.*.observaciones' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $venta = Venta::findOrFail($request->venta_id);
            
            // Crear la devolución
            $devolucion = new Devolucion([
                'venta_id' => $request->venta_id,
                'numero_devolucion' => $this->generarNumeroDevolucion(),
                'fecha_devolucion' => now(),
                'motivo_devolucion' => $request->motivo_devolucion,
                'observaciones' => $request->observaciones,
                'estado' => 'PENDIENTE',
                'procesado_por' => Auth::id(),
            ]);
            
            $devolucion->save();
            
            $montoTotalDevolucion = 0;
            
            // Procesar cada producto devuelto
            foreach ($request->productos as $producto) {
                $detalleVenta = DetalleVenta::where('venta_id', $request->venta_id)
                    ->where('producto_id', $producto['producto_id'])
                    ->first();
                
                if (!$detalleVenta) {
                    throw new \Exception("Producto no encontrado en la venta.");
                }
                
                // Verificar cantidad disponible
                $cantidadYaDevuelta = DetalleDevolucion::whereHas('devolucion', function($q) use ($request) {
                        $q->where('venta_id', $request->venta_id)
                          ->where('estado', '!=', 'CANCELADA');
                    })
                    ->where('producto_id', $producto['producto_id'])
                    ->sum('cantidad_devuelta');
                    
                $cantidadDisponible = $detalleVenta->cantidad - $cantidadYaDevuelta;
                
                if ($producto['cantidad'] > $cantidadDisponible) {
                    throw new \Exception("La cantidad a devolver excede la disponible para el producto.");
                }
                
                // Crear detalle de devolución
                $detalleDevolucion = new DetalleDevolucion([
                    'devolucion_id' => $devolucion->id_devolucion,
                    'producto_id' => $producto['producto_id'],
                    'cantidad_devuelta' => $producto['cantidad'],
                    'precio_unitario' => $detalleVenta->precio_unitario,
                    'observaciones' => $producto['observaciones'] ?? null,
                    'condicion_producto' => $producto['condicion'],
                    'retornar_inventario' => $producto['retornar_inventario'] ?? false,
                ]);
                
                $detalleDevolucion->save();
                
                $montoTotalDevolucion += $detalleDevolucion->subtotal;
                
                // Retornar al inventario si corresponde
                if ($detalleDevolucion->puedeRetornarInventario()) {
                    MovimientoStock::create([
                        'producto_id' => $producto['producto_id'],
                        'tipo_movimiento' => 'DEVOLUCION',
                        'cantidad' => $producto['cantidad'],
                        'motivo' => 'Devolución de venta #' . $venta->numero_venta,
                        'referencia_id' => $devolucion->id_devolucion,
                        'registrado_por' => Auth::id(),
                    ]);
                    
                    // Actualizar stock del producto
                    $productoModel = $detalleDevolucion->producto;
                    $productoModel->increment('stock', $producto['cantidad']);
                }
            }
            
            // Actualizar monto total de la devolución
            $devolucion->monto_devuelto = $montoTotalDevolucion;
            $devolucion->save();
            
            DB::commit();
            
            return redirect()->route('devoluciones.show', $devolucion)
                           ->with('success', 'Devolución creada exitosamente.');
                           
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    public function show(Devolucion $devolucion)
    {
        $devolucion->load(['venta.cliente', 'detalles.producto', 'procesadoPor']);
        
        return view('devoluciones.show', compact('devolucion'));
    }
    
    public function aprobar(Devolucion $devolucion)
    {
        if ($devolucion->estado !== 'PENDIENTE') {
            return back()->with('error', 'Solo se pueden aprobar devoluciones pendientes.');
        }
        
        DB::beginTransaction();
        
        try {
            $devolucion->estado = 'APROBADA';
            $devolucion->fecha_aprobacion = now();
            $devolucion->aprobado_por = Auth::id();
            $devolucion->save();
            
            // Procesar reembolso o nota de crédito aquí si es necesario
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Devolución aprobada exitosamente.');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    public function rechazar(Request $request, Devolucion $devolucion)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:500',
        ]);
        
        if ($devolucion->estado !== 'PENDIENTE') {
            return back()->with('error', 'Solo se pueden rechazar devoluciones pendientes.');
        }
        
        DB::beginTransaction();
        
        try {
            // Revertir movimientos de stock
            foreach ($devolucion->detalles as $detalle) {
                if ($detalle->puedeRetornarInventario()) {
                    // Buscar el movimiento de stock y revertirlo
                    $movimiento = MovimientoStock::where('referencia_id', $devolucion->id_devolucion)
                        ->where('producto_id', $detalle->producto_id)
                        ->where('tipo_movimiento', 'DEVOLUCION')
                        ->first();
                        
                    if ($movimiento) {
                        // Crear movimiento contrario
                        MovimientoStock::create([
                            'producto_id' => $detalle->producto_id,
                            'tipo_movimiento' => 'AJUSTE_NEGATIVO',
                            'cantidad' => $detalle->cantidad_devuelta,
                            'motivo' => 'Rechazo de devolución #' . $devolucion->numero_devolucion,
                            'referencia_id' => $devolucion->id_devolucion,
                            'registrado_por' => Auth::id(),
                        ]);
                        
                        // Reducir stock
                        $detalle->producto->decrement('stock', $detalle->cantidad_devuelta);
                        
                        // Marcar movimiento original como revertido
                        $movimiento->delete();
                    }
                }
            }
            
            $devolucion->estado = 'RECHAZADA';
            $devolucion->motivo_rechazo = $request->motivo_rechazo;
            $devolucion->fecha_rechazo = now();
            $devolucion->rechazado_por = Auth::id();
            $devolucion->save();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Devolución rechazada exitosamente.');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    public function cancelar(Devolucion $devolucion)
    {
        if ($devolucion->estado === 'APROBADA') {
            return back()->with('error', 'No se puede cancelar una devolución ya aprobada.');
        }
        
        $devolucion->estado = 'CANCELADA';
        $devolucion->save();
        
        return redirect()->back()->with('success', 'Devolución cancelada exitosamente.');
    }
    
    private function generarNumeroDevolucion()
    {
        $ultimo = Devolucion::orderBy('id_devolucion', 'desc')->first();
        $numero = $ultimo ? $ultimo->id_devolucion + 1 : 1;
        
        return 'DEV-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}