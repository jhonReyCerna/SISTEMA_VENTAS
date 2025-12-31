<?php

namespace App\Exports;

use App\Models\Producto;

class ProductosExport
{
    /**
     * Obtener los productos para exportar
     */
    public function getProductos()
    {
        return Producto::with('categoria')->get();
    }

    /**
     * Convertir los productos a array para Excel
     */
    public function toArray()
    {
        $productos = $this->getProductos();
        $data = [];

        // Encabezados
        $data[] = ['CÓDIGO', 'NOMBRE', 'CATEGORÍA', 'PRECIO (S/)', 'STOCK', 'ESTADO'];

        // Datos
        foreach ($productos as $producto) {
            $data[] = [
                $producto->id_producto ?? 'N/A',
                $producto->nombre,
                $producto->categoria->nombre ?? 'Sin categoría',
                number_format($producto->precio_venta ?? 0, 2),
                $producto->stock,
                $producto->stock > 0 ? 'DISPONIBLE' : 'AGOTADO'
            ];
        }

        return $data;
    }
}
