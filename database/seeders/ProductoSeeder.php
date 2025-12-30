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
        // Electrónica
        Producto::create([
            'id_categoria' => 1,
            'nombre' => 'Laptop HP Pavilion 15',
            'descripcion' => 'Laptop 15.6 pulgadas con procesador Intel i5',
            'precio_compra' => 1200.00,
            'precio_venta' => 1599.00,
            'stock' => 10,
            'stock_minimo' => 3,
            'estado' => 'ACTIVO',
        ]);

        Producto::create([
            'id_categoria' => 1,
            'nombre' => 'Mouse Logitech inalámbrico',
            'descripcion' => 'Mouse inalámbrico con batería de larga duración',
            'precio_compra' => 25.00,
            'precio_venta' => 45.00,
            'stock' => 50,
            'stock_minimo' => 10,
            'estado' => 'ACTIVO',
        ]);

        // Ropa y Accesorios
        Producto::create([
            'id_categoria' => 2,
            'nombre' => 'Polo de algodón',
            'descripcion' => 'Polo 100% algodón disponible en varios colores',
            'precio_compra' => 15.00,
            'precio_venta' => 35.00,
            'stock' => 100,
            'stock_minimo' => 20,
            'estado' => 'ACTIVO',
        ]);

        // Alimentos
        Producto::create([
            'id_categoria' => 3,
            'nombre' => 'Café molido premium',
            'descripcion' => 'Café 100% arábica molido en bolsa de 500g',
            'precio_compra' => 12.00,
            'precio_venta' => 25.00,
            'stock' => 75,
            'stock_minimo' => 15,
            'estado' => 'ACTIVO',
        ]);

        // Libros
        Producto::create([
            'id_categoria' => 4,
            'nombre' => 'El Quijote de Cervantes',
            'descripcion' => 'Novela clásica de literatura universal',
            'precio_compra' => 20.00,
            'precio_venta' => 45.00,
            'stock' => 25,
            'stock_minimo' => 5,
            'estado' => 'ACTIVO',
        ]);

        // Hogar
        Producto::create([
            'id_categoria' => 5,
            'nombre' => 'Lámpara LED de escritorio',
            'descripcion' => 'Lámpara LED ajustable para escritorio',
            'precio_compra' => 30.00,
            'precio_venta' => 65.00,
            'stock' => 35,
            'stock_minimo' => 8,
            'estado' => 'ACTIVO',
        ]);

        // Deportes
        Producto::create([
            'id_categoria' => 6,
            'nombre' => 'Balón de fútbol profesional',
            'descripcion' => 'Balón de fútbol de cuero sintético',
            'precio_compra' => 35.00,
            'precio_venta' => 85.00,
            'stock' => 20,
            'stock_minimo' => 5,
            'estado' => 'ACTIVO',
        ]);

        Producto::create([
            'id_categoria' => 6,
            'nombre' => 'Mancuernas ajustables 10kg',
            'descripcion' => 'Juego de mancuernas ajustables',
            'precio_compra' => 80.00,
            'precio_venta' => 150.00,
            'stock' => 8,
            'stock_minimo' => 2,
            'estado' => 'ACTIVO',
        ]);
    }
}
