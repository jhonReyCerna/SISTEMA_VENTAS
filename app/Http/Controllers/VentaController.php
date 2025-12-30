<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ventas = Venta::with(['cliente', 'usuario'])->orderBy('fecha_venta', 'desc')->paginate(10);
        return view('ventas.index', compact('ventas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::where('estado', 'ACTIVO')->get();
        $productos = Producto::where('estado', 'ACTIVO')->where('stock', '>', 0)->get();
        return view('ventas.create', compact('clientes', 'productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'tipo_comprobante' => 'required|in:BOLETA,FACTURA',
            'total' => 'required|numeric|min:0',
        ]);

        $validated['id_usuario'] = Auth::id();

        Venta::create($validated);

        return redirect()->route('ventas.index')->with('success', 'Venta registrada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'detalles.producto']);
        return view('ventas.show', compact('venta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venta $venta)
    {
        $clientes = Cliente::where('estado', 'ACTIVO')->get();
        return view('ventas.edit', compact('venta', 'clientes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        $validated = $request->validate([
            'id_cliente' => 'required|exists:clientes,id_cliente',
            'tipo_comprobante' => 'required|in:BOLETA,FACTURA',
            'total' => 'required|numeric|min:0',
        ]);

        $venta->update($validated);

        return redirect()->route('ventas.index')->with('success', 'Venta actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        $venta->delete();

        return redirect()->route('ventas.index')->with('success', 'Venta eliminada exitosamente');
    }
}
