<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Exports\ProductosExport;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Producto::with('categoria');
        if (request()->filled('nombre')) {
            $query->where('nombre', 'like', '%' . request('nombre') . '%');
        }
        if (request()->filled('categoria')) {
            $query->where('id_categoria', request('categoria'));
        }
        if (request()->filled('precio_compra')) {
            $query->where('precio_compra', request('precio_compra'));
        }
        if (request()->filled('precio_venta')) {
            $query->where('precio_venta', request('precio_venta'));
        }
        if (request()->filled('estado')) {
            $query->where('estado', request('estado'));
        }
        $productos = $query->paginate(10)->appends(request()->query());
        $categorias = \App\Models\Categoria::all();
        return view('productos.index', compact('productos', 'categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::all();
        return view('productos.create', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:200',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        Producto::create($validated);

        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        $producto->load('categoria');
        return view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $categorias = Categoria::all();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:200',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'estado' => 'required|in:ACTIVO,INACTIVO',
        ]);

        $producto->update($validated);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado exitosamente');
    }

    /**
     * Exportar productos a Excel
     */
    public function exportarExcel()
    {
        $export = new ProductosExport();
        $data = $export->toArray();
        
        $nombreArchivo = 'productos_' . date('Y-m-d_H-i-s') . '.csv';
        
        $response = new StreamedResponse(function() use ($data) {
            $handle = fopen('php://output', 'w');
            
            // BOM para UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($data as $row) {
                fputcsv($handle, $row, ';');
            }
            
            fclose($handle);
        });
        
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"');
        
        return $response;
    }
}
