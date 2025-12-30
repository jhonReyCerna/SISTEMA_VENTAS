<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'nombre' => 'Admin',
            'descripcion' => 'Administrador del sistema',
        ]);

        Role::create([
            'nombre' => 'Vendedor',
            'descripcion' => 'Encargado de ventas',
        ]);

        Role::create([
            'nombre' => 'Supervisor',
            'descripcion' => 'Supervisor de ventas',
        ]);
    }
}
