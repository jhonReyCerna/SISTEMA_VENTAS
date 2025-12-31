<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nombres = [
            'Laptop HP Pavilion 15', 'Mouse Logitech inalámbrico', 'Polo Algodón', 'Zapatillas Nike', 'Smartphone Samsung',
            'Libro Laravel', 'Silla de Oficina', 'Taladro Bosch', 'Shampoo Pantene', 'Bicicleta Montaña',
            'Cama para Mascotas', 'Cámara Canon', 'Juguete Lego', 'Cereal Nestlé', 'Maleta Samsonite'
        ];
        $descripciones = [
            'Laptop 15.6 pulgadas con procesador Intel i5',
            'Mouse inalámbrico con batería de larga duración',
            'Polo 100% algodón',
            'Zapatillas deportivas para correr',
            'Smartphone Android 128GB',
            'Libro de desarrollo web con Laravel',
            'Silla ergonómica para oficina',
            'Taladro percutor 500W',
            'Shampoo para todo tipo de cabello',
            'Bicicleta de montaña 21 velocidades',
            'Cama suave para mascotas',
            'Cámara fotográfica profesional',
            'Juguete de construcción Lego',
            'Cereal integral para desayuno',
            'Maleta de viaje resistente',
        ];
        for ($i = 0; $i < 15; $i++) {
            Producto::create([
                'id_categoria' => ($i % 15) + 1,
                'nombre' => $nombres[$i],
                'descripcion' => $descripciones[$i],
                'precio_compra' => rand(10, 1000),
                'precio_venta' => rand(20, 1500),
                'stock' => rand(5, 100),
                'stock_minimo' => rand(1, 10),
                'estado' => 'ACTIVO',
            ]);
        }
    }
}
