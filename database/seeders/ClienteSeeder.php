<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cliente::create([
            'tipo_documento' => 'DNI',
            'numero_documento' => '12345678',
            'nombre' => 'Juan Pérez García',
            'direccion' => 'Calle Principal 123, Apt 4',
            'telefono' => '987654321',
            'email' => 'juan.perez@email.com',
            'estado' => 'ACTIVO',
        ]);

        Cliente::create([
            'tipo_documento' => 'DNI',
            'numero_documento' => '87654321',
            'nombre' => 'María López Martínez',
            'direccion' => 'Avenida Central 456',
            'telefono' => '987123456',
            'email' => 'maria.lopez@email.com',
            'estado' => 'ACTIVO',
        ]);

        Cliente::create([
            'tipo_documento' => 'RUC',
            'numero_documento' => '20123456789',
            'nombre' => 'Distribuidora Silva S.A.C.',
            'direccion' => 'Carrera 789, Local 10',
            'telefono' => '965432100',
            'email' => 'contacto@silva.com',
            'estado' => 'ACTIVO',
        ]);

        Cliente::create([
            'tipo_documento' => 'DNI',
            'numero_documento' => '45678901',
            'nombre' => 'Carlos Rodríguez Sánchez',
            'direccion' => 'Pasaje Norte 234',
            'telefono' => '956789012',
            'email' => 'carlos.rodriguez@email.com',
            'estado' => 'ACTIVO',
        ]);

        Cliente::create([
            'tipo_documento' => 'RUC',
            'numero_documento' => '20987654321',
            'nombre' => 'Empresa Comercial Andina',
            'direccion' => 'Jr. Comercio 567',
            'telefono' => '942345678',
            'email' => 'ventas@andina.com',
            'estado' => 'ACTIVO',
        ]);

        Cliente::create([
            'tipo_documento' => 'DNI',
            'numero_documento' => '23456789',
            'nombre' => 'Ana Fernández Ruiz',
            'direccion' => 'Urb. San Felipe 890',
            'telefono' => '923456789',
            'email' => 'ana.fernandez@email.com',
            'estado' => 'INACTIVO',
        ]);
    }
}
