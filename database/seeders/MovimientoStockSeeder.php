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
        for ($i = 1; $i <= 15; $i++) {
            MovimientoStock::create([
                'id_producto' => $i,
                'tipo_movimiento' => $i % 2 == 0 ? 'ENTRADA' : 'SALIDA',
                'cantidad' => rand(1, 100),
                'motivo' => $i % 2 == 0 ? 'Stock inicial' : 'Venta',
            ]);
        }
    }
}
