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
        $nombres = [
            'Electrónica', 'Ropa y Accesorios', 'Alimentos', 'Libros', 'Hogar',
            'Deportes', 'Juguetes', 'Belleza', 'Salud', 'Automotriz',
            'Mascotas', 'Jardinería', 'Ferretería', 'Oficina', 'Viajes'
        ];
        $descripciones = [
            'Productos electrónicos y dispositivos',
            'Prendas de vestir y accesorios varios',
            'Productos alimenticios y bebidas',
            'Libros y material de lectura',
            'Artículos para el hogar',
            'Equipos y accesorios deportivos',
            'Juguetes y juegos para niños',
            'Productos de belleza y cuidado personal',
            'Productos de salud y bienestar',
            'Accesorios y repuestos automotrices',
            'Productos para mascotas',
            'Herramientas y productos de jardinería',
            'Herramientas y materiales de ferretería',
            'Suministros de oficina',
            'Artículos y servicios de viaje',
        ];
        for ($i = 0; $i < 15; $i++) {
            Categoria::create([
                'nombre' => $nombres[$i],
                'descripcion' => $descripciones[$i],
                'estado' => 'ACTIVO',
            ]);
        }
    }
}
