<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Categoria::create([
            'nombre' => 'Electrónica',
            'descripcion' => 'Productos electrónicos y dispositivos',
            'estado' => 'ACTIVO',
        ]);

        Categoria::create([
            'nombre' => 'Ropa y Accesorios',
            'descripcion' => 'Prendas de vestir y accesorios varios',
            'estado' => 'ACTIVO',
        ]);

        Categoria::create([
            'nombre' => 'Alimentos',
            'descripcion' => 'Productos alimenticios y bebidas',
            'estado' => 'ACTIVO',
        ]);

        Categoria::create([
            'nombre' => 'Libros',
            'descripcion' => 'Libros y material de lectura',
            'estado' => 'ACTIVO',
        ]);

        Categoria::create([
            'nombre' => 'Hogar',
            'descripcion' => 'Artículos para el hogar y decoración',
            'estado' => 'ACTIVO',
        ]);

        Categoria::create([
            'nombre' => 'Deportes',
            'descripcion' => 'Equipos y accesorios deportivos',
            'estado' => 'ACTIVO',
        ]);
    }
}
