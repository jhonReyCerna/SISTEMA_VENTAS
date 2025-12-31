<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CuponController extends Controller
{
    public function index(Request $request)
    {
        $query = Cupon::query();
        
        // Filtros de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('tipo')) {
            $query->where('tipo_cupon', $request->tipo);
        }
        
        if ($request->filled('estado')) {
            switch ($request->estado) {
                case 'activo':
                    $query->activos();
                    break;
                case 'vencido':
                    $query->vencidos();
                    break;
                case 'agotado':
                    $query->agotados();
                    break;
                case 'inactivo':
                    $query->where('activo', false);
                    break;
            }
        }
        
        $cupones = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('cupones.index', compact('cupones'));
    }
    
    public function create()
    {
        $clientes = Cliente::activos()->orderBy('nombre')->get();
        
        return view('cupones.create', compact('clientes'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'nullable|string|max:20|unique:cupones,codigo',
            'tipo_cupon' => 'required|in:PORCENTAJE,MONTO_FIJO,ENVIO_GRATIS',
            'valor_descuento' => 'required_unless:tipo_cupon,ENVIO_GRATIS|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'limite_uso' => 'nullable|integer|min:1',
            'limite_uso_cliente' => 'nullable|integer|min:1',
            'monto_minimo_compra' => 'nullable|numeric|min:0',
            'cliente_especifico_id' => 'nullable|exists:clientes,id_cliente',
            'activo' => 'boolean',
        ]);
        
        // Generar código automáticamente si no se proporciona
        $data = $request->all();
        if (empty($data['codigo'])) {
            do {
                $data['codigo'] = $this->generarCodigo();
            } while (Cupon::where('codigo', $data['codigo'])->exists());
        }
        
        if ($request->tipo_cupon === 'PORCENTAJE' && $request->valor_descuento > 100) {
            return back()->withErrors(['valor_descuento' => 'El porcentaje de descuento no puede ser mayor a 100%.']);
        }
        
        $cupon = new Cupon($data);
        $cupon->creado_por = Auth::id();
        $cupon->save();
        
        return redirect()->route('cupones.index')->with('success', 'Cupón creado exitosamente.');
    }
    
    public function show(Cupon $cupon)
    {
        $cupon->load(['ventasUsadas.cliente', 'creadoPor']);
        
        return view('cupones.show', compact('cupon'));
    }
    
    public function edit(Cupon $cupon)
    {
        $clientes = Cliente::activos()->orderBy('nombre')->get();
        
        return view('cupones.edit', compact('cupon', 'clientes'));
    }
    
    public function update(Request $request, Cupon $cupon)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => "required|string|max:20|unique:cupones,codigo,{$cupon->id_cupon},id_cupon",
            'tipo_cupon' => 'required|in:PORCENTAJE,MONTO_FIJO,ENVIO_GRATIS',
            'valor_descuento' => 'required_unless:tipo_cupon,ENVIO_GRATIS|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'limite_uso' => 'nullable|integer|min:1',
            'limite_uso_cliente' => 'nullable|integer|min:1',
            'monto_minimo_compra' => 'nullable|numeric|min:0',
            'cliente_especifico_id' => 'nullable|exists:clientes,id_cliente',
            'activo' => 'boolean',
        ]);
        
        $cupon->update($request->all());
        
        return redirect()->route('cupones.index')->with('success', 'Cupón actualizado exitosamente.');
    }
    
    public function destroy(Cupon $cupon)
    {
        if ($cupon->ventasUsadas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el cupón porque ha sido utilizado en ventas.');
        }
        
        $cupon->delete();
        
        return redirect()->route('cupones.index')->with('success', 'Cupón eliminado exitosamente.');
    }
    
    public function toggle(Cupon $cupon)
    {
        $cupon->activo = !$cupon->activo;
        $cupon->save();
        
        $estado = $cupon->activo ? 'activado' : 'desactivado';
        
        return redirect()->back()->with('success', "Cupón {$estado} exitosamente.");
    }
    
    public function validar(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
            'cliente_id' => 'nullable|exists:clientes,id_cliente',
            'monto_compra' => 'required|numeric|min:0',
        ]);
        
        $cupon = Cupon::where('codigo', $request->codigo)->first();
        
        if (!$cupon) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Cupón no encontrado.'
            ]);
        }
        
        $validacion = $cupon->validar($request->cliente_id, $request->monto_compra);
        
        return response()->json($validacion);
    }
    
    public function aplicar(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string',
            'cliente_id' => 'nullable|exists:clientes,id_cliente',
            'monto_compra' => 'required|numeric|min:0',
        ]);
        
        $cupon = Cupon::where('codigo', $request->codigo)->first();
        
        if (!$cupon) {
            return response()->json([
                'error' => 'Cupón no encontrado.'
            ], 404);
        }
        
        $descuento = $cupon->calcularDescuento($request->monto_compra);
        
        return response()->json([
            'cupon' => $cupon,
            'descuento' => $descuento,
            'monto_final' => $request->monto_compra - $descuento
        ]);
    }
    
    public function generar(Request $request)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1|max:100',
            'tipo_cupon' => 'required|in:PORCENTAJE,MONTO_FIJO',
            'valor_descuento' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'limite_uso' => 'nullable|integer|min:1',
            'monto_minimo_compra' => 'nullable|numeric|min:0',
        ]);
        
        $cupones = [];
        
        for ($i = 0; $i < $request->cantidad; $i++) {
            do {
                $codigo = $this->generarCodigo();
            } while (Cupon::where('codigo', $codigo)->exists());
            
            $cupon = new Cupon([
                'nombre' => "Cupón generado #" . ($i + 1),
                'codigo' => $codigo,
                'tipo_cupon' => $request->tipo_cupon,
                'valor_descuento' => $request->valor_descuento,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'limite_uso' => $request->limite_uso,
                'monto_minimo_compra' => $request->monto_minimo_compra,
                'activo' => true,
                'creado_por' => Auth::id(),
            ]);
            
            $cupon->save();
            $cupones[] = $cupon;
        }
        
        return redirect()->route('cupones.index')->with('success', "{$request->cantidad} cupones generados exitosamente.");
    }
    
    private function generarCodigo($longitud = 8)
    {
        return strtoupper(Str::random($longitud));
    }
}