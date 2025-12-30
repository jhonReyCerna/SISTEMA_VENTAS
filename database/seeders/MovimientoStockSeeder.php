<?php

namespace Database\Seeders;

use App\Models\MovimientoStock;
use Illuminate\Database\Seeder;

class MovimientoStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Entradas de stock inicial
        MovimientoStock::create([
            'id_producto' => 1, // Laptop HP
            'tipo_movimiento' => 'ENTRADA',
            'cantidad' => 50,
            'motivo' => 'Stock inicial',
        ]);

        MovimientoStock::create([
            'id_producto' => 2, // Mouse Logitech
            'tipo_movimiento' => 'ENTRADA',
            'cantidad' => 150,
            'motivo' => 'Stock inicial',
        ]);

        MovimientoStock::create([
            'id_producto' => 3, // Polo
            'tipo_movimiento' => 'ENTRADA',
            'cantidad' => 200,
            'motivo' => 'Stock inicial',
        ]);

        // Salidas por ventas
        MovimientoStock::create([
            'id_producto' => 1, // Laptop HP
            'tipo_movimiento' => 'SALIDA',
            'cantidad' => 1,
            'motivo' => 'Venta realizada',
        ]);

        MovimientoStock::create([
            'id_producto' => 2, // Mouse
            'tipo_movimiento' => 'SALIDA',
            'cantidad' => 1,
            'motivo' => 'Venta realizada',
        ]);

        MovimientoStock::create([
            'id_producto' => 3, // Polo
            'tipo_movimiento' => 'SALIDA',
            'cantidad' => 7,
            'motivo' => 'Ventas realizadas',
        ]);

        // Entrada por compra
        MovimientoStock::create([
            'id_producto' => 4, // Café
            'tipo_movimiento' => 'ENTRADA',
            'cantidad' => 100,
            'motivo' => 'Compra a proveedor',
        ]);

        // Salida por compra de cliente
        MovimientoStock::create([
            'id_producto' => 4, // Café
            'tipo_movimiento' => 'SALIDA',
            'cantidad' => 10,
            'motivo' => 'Venta realizada',
        ]);

        // Entradas adicionales
        MovimientoStock::create([
            'id_producto' => 5, // El Quijote
            'tipo_movimiento' => 'ENTRADA',
            'cantidad' => 80,
            'motivo' => 'Stock inicial',
        ]);

        MovimientoStock::create([
            'id_producto' => 6, // Lámpara LED
            'tipo_movimiento' => 'ENTRADA',
            'cantidad' => 60,
            'motivo' => 'Stock inicial',
        ]);

        // Salidas
        MovimientoStock::create([
            'id_producto' => 5, // El Quijote
            'tipo_movimiento' => 'SALIDA',
            'cantidad' => 2,
            'motivo' => 'Venta realizada',
        ]);

        MovimientoStock::create([
            'id_producto' => 6, // Lámpara LED
            'tipo_movimiento' => 'SALIDA',
            'cantidad' => 2,
            'motivo' => 'Venta realizada',
        ]);
    }
}
