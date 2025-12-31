<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\VentasExport;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Venta::with(['cliente', 'usuario'])->orderBy('fecha_venta', 'desc');
        if (request()->filled('fecha')) {
            $query->whereDate('fecha_venta', request('fecha'));
        }
        if (request()->filled('cliente')) {
            $query->whereHas('cliente', function($q) {
                $q->where('nombre', 'like', '%' . request('cliente') . '%');
            });
        }
        if (request()->filled('usuario')) {
            $query->whereHas('usuario', function($q) {
                $q->where('nombre', 'like', '%' . request('usuario') . '%');
            });
        }
        if (request()->filled('comprobante')) {
            $query->where('tipo_comprobante', request('comprobante'));
        }
        $ventas = $query->paginate(10)->appends(request()->query());
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

    /**
     * Generar PDF de la factura
     */
    public function generarPDF(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'detalles.producto']);
        
        $pdf = Pdf::loadView('ventas.factura_pdf', compact('venta'));
        
        return $pdf->download('factura_' . str_pad($venta->id_venta, 8, '0', STR_PAD_LEFT) . '.pdf');
    }

    /**
     * Mostrar ticket de venta para imprimir
     */
    public function ticket(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'detalles.producto']);
        
        return view('ventas.ticket', compact('venta'));
    }

    /**
     * Exportar ventas a Excel
     */
    public function exportarExcel(Request $request)
    {
        $fecha_inicio = $request->input('fecha_inicio');
        $fecha_fin = $request->input('fecha_fin');
        
        $export = new VentasExport($fecha_inicio, $fecha_fin);
        $data = $export->toArray();
        
        $nombreArchivo = 'ventas_' . date('Y-m-d_H-i-s') . '.csv';
        
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
