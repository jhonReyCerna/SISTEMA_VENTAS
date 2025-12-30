<?php

namespace Database\Seeders;

use App\Models\DetalleVenta;
use Illuminate\Database\Seeder;

class DetalleVentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Detalles para Venta 1 (Total: 1644.00)
        DetalleVenta::create([
            'id_venta' => 1,
            'id_producto' => 1, // Laptop HP
            'cantidad' => 1,
            'precio_unitario' => 1599.00,
            'subtotal' => 1599.00,
        ]);

        DetalleVenta::create([
            'id_venta' => 1,
            'id_producto' => 2, // Mouse
            'cantidad' => 1,
            'precio_unitario' => 45.00,
            'subtotal' => 45.00,
        ]);

        // Detalles para Venta 2 (Total: 70.00)
        DetalleVenta::create([
            'id_venta' => 2,
            'id_producto' => 3, // Polo
            'cantidad' => 2,
            'precio_unitario' => 35.00,
            'subtotal' => 70.00,
        ]);

        // Detalles para Venta 3 (Total: 560.00)
        DetalleVenta::create([
            'id_venta' => 3,
            'id_producto' => 4, // Café
            'cantidad' => 10,
            'precio_unitario' => 25.00,
            'subtotal' => 250.00,
        ]);

        DetalleVenta::create([
            'id_venta' => 3,
            'id_producto' => 3, // Polo
            'cantidad' => 5,
            'precio_unitario' => 35.00,
            'subtotal' => 175.00,
        ]);

        DetalleVenta::create([
            'id_venta' => 3,
            'id_producto' => 6, // Lámpara
            'cantidad' => 2,
            'precio_unitario' => 65.00,
            'subtotal' => 130.00,
        ]);

        // Detalles para Venta 4 (Total: 90.00)
        DetalleVenta::create([
            'id_venta' => 4,
            'id_producto' => 5, // El Quijote
            'cantidad' => 2,
            'precio_unitario' => 45.00,
            'subtotal' => 90.00,
        ]);

        // Detalles para Venta 5 (Total: 235.00)
        DetalleVenta::create([
            'id_venta' => 5,
            'id_producto' => 7, // Balón de fútbol
            'cantidad' => 1,
            'precio_unitario' => 85.00,
            'subtotal' => 85.00,
        ]);

        DetalleVenta::create([
            'id_venta' => 5,
            'id_producto' => 8, // Mancuernas
            'cantidad' => 1,
            'precio_unitario' => 150.00,
            'subtotal' => 150.00,
        ]);
    }
}
