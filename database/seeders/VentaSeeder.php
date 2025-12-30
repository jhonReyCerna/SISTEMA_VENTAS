<?php

namespace Database\Seeders;

use App\Models\Venta;
use Illuminate\Database\Seeder;

class VentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Venta::create([
            'id_cliente' => 1,
            'id_usuario' => 1,
            'total' => 1644.00,
            'tipo_comprobante' => 'BOLETA',
        ]);

        Venta::create([
            'id_cliente' => 2,
            'id_usuario' => 1,
            'total' => 70.00,
            'tipo_comprobante' => 'BOLETA',
        ]);

        Venta::create([
            'id_cliente' => 3,
            'id_usuario' => 1,
            'total' => 560.00,
            'tipo_comprobante' => 'FACTURA',
        ]);

        Venta::create([
            'id_cliente' => 4,
            'id_usuario' => 1,
            'total' => 90.00,
            'tipo_comprobante' => 'BOLETA',
        ]);

        Venta::create([
            'id_cliente' => 5,
            'id_usuario' => 1,
            'total' => 235.00,
            'tipo_comprobante' => 'FACTURA',
        ]);
    }
}
