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
        for ($i = 1; $i <= 15; $i++) {
            Venta::create([
                'id_cliente' => $i,
                'id_usuario' => 1,
                'total' => rand(50, 2000),
                'tipo_comprobante' => $i % 2 == 0 ? 'BOLETA' : 'FACTURA',
            ]);
        }

        Venta::create([
            'id_cliente' => 5,
            'id_usuario' => 1,
            'total' => 235.00,
            'tipo_comprobante' => 'FACTURA',
        ]);
    }
}
