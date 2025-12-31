<?php

namespace App\Exports;

use App\Models\Venta;
use Carbon\Carbon;

class VentasExport
{
    protected $fecha_inicio;
    protected $fecha_fin;

    public function __construct($fecha_inicio = null, $fecha_fin = null)
    {
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    /**
     * Obtener las ventas para exportar
     */
    public function getVentas()
    {
        $query = Venta::with(['cliente', 'usuario']);

        if ($this->fecha_inicio && $this->fecha_fin) {
            $query->whereBetween('fecha_venta', [$this->fecha_inicio, $this->fecha_fin]);
        }

        return $query->orderBy('fecha_venta', 'desc')->get();
    }

    /**
     * Convertir las ventas a array para Excel
     */
    public function toArray()
    {
        $ventas = $this->getVentas();
        $data = [];

        // Encabezados
        $data[] = ['N° VENTA', 'FECHA', 'CLIENTE', 'VENDEDOR', 'TOTAL (S/)', 'ESTADO'];

        // Datos
        foreach ($ventas as $venta) {
            $data[] = [
                str_pad($venta->id_venta, 8, '0', STR_PAD_LEFT),
                Carbon::parse($venta->fecha_venta)->format('d/m/Y H:i'),
                $venta->cliente->nombre ?? 'N/A',
                $venta->usuario->nombre ?? 'N/A',
                number_format($venta->total, 2),
                'COMPLETADA'
            ];
        }

        return $data;
    }
}
