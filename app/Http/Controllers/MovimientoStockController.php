<?php

namespace App\Http\Controllers;

use App\Models\MovimientoStock;
use App\Models\Producto;
use Illuminate\Http\Request;

class MovimientoStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movimientos = MovimientoStock::with('producto')->orderBy('fecha_movimiento', 'desc')->paginate(10);
        return view('movimientos.index', compact('movimientos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productos = Producto::where('estado', 'ACTIVO')->get();
        return view('movimientos.create', compact('productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
            'tipo_movimiento' => 'required|in:ENTRADA,SALIDA',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'nullable|string|max:150',
        ]);

        // Obtener el producto
        $producto = Producto::findOrFail($validated['id_producto']);

        // Si es SALIDA, verificar que hay stock suficiente
        if ($validated['tipo_movimiento'] === 'SALIDA') {
            if ($producto->stock < $validated['cantidad']) {
                return back()->withErrors(['cantidad' => 'Stock insuficiente. Stock actual: ' . $producto->stock])->withInput();
            }
        }

        // Crear el movimiento
        MovimientoStock::create($validated);

        // Actualizar el stock del producto
        if ($validated['tipo_movimiento'] === 'ENTRADA') {
            $producto->stock += $validated['cantidad'];
        } else {
            $producto->stock -= $validated['cantidad'];
        }
        $producto->save();

        return redirect()->route('movimientos.index')->with('success', 'Movimiento de stock registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $movimiento = MovimientoStock::with('producto')->findOrFail($id);
        return view('movimientos.show', compact('movimiento'));
    }
}
