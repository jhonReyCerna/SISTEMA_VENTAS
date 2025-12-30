<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar RoleSeeder primero
        $this->call(RoleSeeder::class);

        // User::factory(10)->create();

        User::factory()->create([
            'nombre' => 'Test User',
            'email' => 'test@example.com',
            'id_rol' => 1,
            'estado' => 'ACTIVO',
        ]);
    }
}
