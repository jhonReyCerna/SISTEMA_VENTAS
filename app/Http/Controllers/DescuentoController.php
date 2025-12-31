<?php

namespace App\Http\Controllers;

use App\Models\Descuento;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DescuentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Descuento::with(['cliente', 'producto', 'categoria']);
        
        // Filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('tipo')) {
            $query->where('tipo_descuento', $request->tipo);
        }
        
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }
        
        $descuentos = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('descuentos.index', compact('descuentos'));
    }
    
    public function create()
    {
        $clientes = Cliente::activos()->orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        
        return view('descuentos.create', compact('clientes', 'productos', 'categorias'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo_descuento' => 'required|in:PORCENTAJE,MONTO_FIJO',
            'valor_descuento' => 'required|numeric|min:0',
            'aplica_a' => 'required|in:GENERAL,CLIENTE,PRODUCTO,CATEGORIA',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'cliente_id' => 'nullable|exists:clientes,id_cliente',
            'producto_id' => 'nullable|exists:productos,id_producto',
            'categoria_id' => 'nullable|exists:categorias,id_categoria',
            'cantidad_minima' => 'nullable|integer|min:1',
            'monto_minimo_compra' => 'nullable|numeric|min:0',
            'activo' => 'boolean',
        ]);
        
        // Validaciones especiales
        if ($request->aplica_a === 'CLIENTE' && !$request->cliente_id) {
            return back()->withErrors(['cliente_id' => 'Debe seleccionar un cliente cuando el descuento aplica a cliente específico.']);
        }
        
        if ($request->aplica_a === 'PRODUCTO' && !$request->producto_id) {
            return back()->withErrors(['producto_id' => 'Debe seleccionar un producto cuando el descuento aplica a producto específico.']);
        }
        
        if ($request->aplica_a === 'CATEGORIA' && !$request->categoria_id) {
            return back()->withErrors(['categoria_id' => 'Debe seleccionar una categoría cuando el descuento aplica a categoría específica.']);
        }
        
        if ($request->tipo_descuento === 'PORCENTAJE' && $request->valor_descuento > 100) {
            return back()->withErrors(['valor_descuento' => 'El porcentaje de descuento no puede ser mayor a 100%.']);
        }
        
        $descuento = new Descuento($request->all());
        $descuento->creado_por = Auth::id();
        $descuento->save();
        
        return redirect()->route('descuentos.index')->with('success', 'Descuento creado exitosamente.');
    }
    
    public function show(Descuento $descuento)
    {
        return view('descuentos.show', compact('descuento'));
    }
    
    public function edit(Descuento $descuento)
    {
        $clientes = Cliente::activos()->orderBy('nombre')->get();
        $productos = Producto::activos()->orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        
        return view('descuentos.edit', compact('descuento', 'clientes', 'productos', 'categorias'));
    }
    
    public function update(Request $request, Descuento $descuento)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo_descuento' => 'required|in:PORCENTAJE,MONTO_FIJO',
            'valor_descuento' => 'required|numeric|min:0',
            'aplica_a' => 'required|in:GENERAL,CLIENTE,PRODUCTO,CATEGORIA',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'cliente_id' => 'nullable|exists:clientes,id_cliente',
            'producto_id' => 'nullable|exists:productos,id_producto',
            'categoria_id' => 'nullable|exists:categorias,id_categoria',
            'cantidad_minima' => 'nullable|integer|min:1',
            'monto_minimo_compra' => 'nullable|numeric|min:0',
            'activo' => 'boolean',
        ]);
        
        $descuento->update($request->all());
        
        return redirect()->route('descuentos.index')->with('success', 'Descuento actualizado exitosamente.');
    }
    
    public function destroy(Descuento $descuento)
    {
        if ($descuento->tieneVentasAsociadas()) {
            return back()->with('error', 'No se puede eliminar el descuento porque tiene ventas asociadas.');
        }
        
        $descuento->delete();
        
        return redirect()->route('descuentos.index')->with('success', 'Descuento eliminado exitosamente.');
    }
    
    public function toggle(Descuento $descuento)
    {
        $descuento->activo = !$descuento->activo;
        $descuento->save();
        
        $estado = $descuento->activo ? 'activado' : 'desactivado';
        
        return redirect()->back()->with('success', "Descuento {$estado} exitosamente.");
    }
    
    public function aplicables(Request $request)
    {
        $clienteId = $request->get('cliente_id');
        $productoId = $request->get('producto_id');
        $montoCompra = $request->get('monto_compra', 0);
        $cantidad = $request->get('cantidad', 1);
        
        $descuentos = Descuento::aplicables($clienteId, $productoId, $montoCompra, $cantidad)->get();
        
        return response()->json($descuentos);
    }
}