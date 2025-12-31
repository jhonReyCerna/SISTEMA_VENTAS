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
        $nombres = [
            'Juan Pérez García', 'María López Martínez', 'Carlos Ramírez', 'Ana Torres', 'Pedro Castillo',
            'Lucía Fernández', 'Miguel Rojas', 'Sofía Herrera', 'Luis Mendoza', 'Elena Vargas',
            'Jorge Salinas', 'Patricia Guzmán', 'Ricardo Díaz', 'Gabriela Soto', 'Fernando Paredes'
        ];
        $documentos = [
            '12345678', '87654321', '11223344', '22334455', '33445566',
            '44556677', '55667788', '66778899', '77889900', '88990011',
            '99001122', '10111213', '12131415', '13141516', '14151617'
        ];
        $emails = [
            'juan.perez@email.com', 'maria.lopez@email.com', 'carlos.ramirez@email.com', 'ana.torres@email.com', 'pedro.castillo@email.com',
            'lucia.fernandez@email.com', 'miguel.rojas@email.com', 'sofia.herrera@email.com', 'luis.mendoza@email.com', 'elena.vargas@email.com',
            'jorge.salinas@email.com', 'patricia.guzman@email.com', 'ricardo.diaz@email.com', 'gabriela.soto@email.com', 'fernando.paredes@email.com'
        ];
        for ($i = 0; $i < 15; $i++) {
            Cliente::create([
                'tipo_documento' => $i < 10 ? 'DNI' : 'RUC',
                'numero_documento' => $documentos[$i],
                'nombre' => $nombres[$i],
                'direccion' => 'Calle ' . chr(65 + $i) . ' #'.($i+10),
                'telefono' => '9' . str_pad((string)($i+10000000), 8, '0', STR_PAD_LEFT),
                'email' => $emails[$i],
                'estado' => 'ACTIVO',
            ]);
        }
    }
}
