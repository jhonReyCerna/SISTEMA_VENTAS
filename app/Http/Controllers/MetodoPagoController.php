<?php

namespace App\Http\Controllers;

use App\Models\MetodoPago;
use Illuminate\Http\Request;

class MetodoPagoController extends Controller
{
    public function index(Request $request)
    {
        $query = MetodoPago::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }
        
        $metodosPago = $query->orderBy('orden')->orderBy('nombre')->paginate(15);
        
        return view('metodos-pago.index', compact('metodosPago'));
    }
    
    public function create()
    {
        return view('metodos-pago.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50|unique:metodos_pago,nombre',
            'descripcion' => 'nullable|string',
            'requiere_referencia' => 'boolean',
            'comision_porcentaje' => 'nullable|numeric|min:0|max:100',
            'comision_fija' => 'nullable|numeric|min:0',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
        ]);
        
        $metodoPago = new MetodoPago($request->all());
        
        // Si no se especifica orden, asignar el siguiente disponible
        if (!$request->filled('orden')) {
            $metodoPago->orden = (MetodoPago::max('orden') ?? 0) + 1;
        }
        
        $metodoPago->save();
        
        return redirect()->route('metodos-pago.index')->with('success', 'Método de pago creado exitosamente.');
    }
    
    public function show(MetodoPago $metodoPago)
    {
        // Obtener estadísticas de uso
        $estadisticas = [
            'total_ventas' => $metodoPago->ventas()->count(),
            'monto_total' => $metodoPago->ventas()->sum('total'),
            'comision_total' => $metodoPago->calcularComisionTotal(),
            'uso_mensual' => $metodoPago->ventas()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
        
        return view('metodos-pago.show', compact('metodoPago', 'estadisticas'));
    }
    
    public function edit(MetodoPago $metodoPago)
    {
        return view('metodos-pago.edit', compact('metodoPago'));
    }
    
    public function update(Request $request, MetodoPago $metodoPago)
    {
        $request->validate([
            'nombre' => "required|string|max:50|unique:metodos_pago,nombre,{$metodoPago->id_metodo_pago},id_metodo_pago",
            'descripcion' => 'nullable|string',
            'requiere_referencia' => 'boolean',
            'comision_porcentaje' => 'nullable|numeric|min:0|max:100',
            'comision_fija' => 'nullable|numeric|min:0',
            'orden' => 'nullable|integer|min:0',
            'activo' => 'boolean',
        ]);
        
        $metodoPago->update($request->all());
        
        return redirect()->route('metodos-pago.index')->with('success', 'Método de pago actualizado exitosamente.');
    }
    
    public function destroy(MetodoPago $metodoPago)
    {
        // Verificar si tiene ventas asociadas
        if ($metodoPago->ventas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el método de pago porque tiene ventas asociadas.');
        }
        
        $metodoPago->delete();
        
        return redirect()->route('metodos-pago.index')->with('success', 'Método de pago eliminado exitosamente.');
    }
    
    public function toggle(MetodoPago $metodoPago)
    {
        $metodoPago->activo = !$metodoPago->activo;
        $metodoPago->save();
        
        $estado = $metodoPago->activo ? 'activado' : 'desactivado';
        
        return redirect()->back()->with('success', "Método de pago {$estado} exitosamente.");
    }
    
    public function reordenar(Request $request)
    {
        $request->validate([
            'orden' => 'required|array',
            'orden.*' => 'required|exists:metodos_pago,id_metodo_pago',
        ]);
        
        foreach ($request->orden as $index => $metodoPagoId) {
            MetodoPago::where('id_metodo_pago', $metodoPagoId)
                ->update(['orden' => $index + 1]);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function activos()
    {
        $metodos = MetodoPago::activos()->orderBy('orden')->get();
        
        return response()->json($metodos);
    }
    
    public function calcularComision(Request $request)
    {
        $request->validate([
            'metodo_pago_id' => 'required|exists:metodos_pago,id_metodo_pago',
            'monto' => 'required|numeric|min:0',
        ]);
        
        $metodoPago = MetodoPago::findOrFail($request->metodo_pago_id);
        $comision = $metodoPago->calcularComision($request->monto);
        
        return response()->json([
            'comision' => $comision,
            'monto_neto' => $request->monto - $comision,
            'metodo_pago' => $metodoPago,
        ]);
    }
}