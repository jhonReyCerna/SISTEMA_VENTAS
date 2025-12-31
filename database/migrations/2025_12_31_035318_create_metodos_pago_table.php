<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('metodos_pago')) {
            Schema::create('metodos_pago', function (Blueprint $table) {
                $table->id('id_metodo_pago');
                $table->string('nombre', 50); // Efectivo, Tarjeta, Transferencia, etc.
                $table->text('descripcion')->nullable();
                $table->boolean('requiere_referencia')->default(false); // Si requiere número de referencia
                $table->boolean('activo')->default(true);
                $table->decimal('comision_porcentaje', 5, 2)->nullable(); // Comisión por usar este método
                $table->decimal('comision_fija', 8, 2)->default(0); // Comisión fija
                $table->integer('orden')->default(0); // Orden de visualización
                $table->timestamps();
                
                $table->unique('nombre');
                $table->index(['activo', 'orden']);
            });
            
            // Insertar métodos de pago básicos
            DB::table('metodos_pago')->insert([
                [
                    'nombre' => 'Efectivo',
                    'descripcion' => 'Pago en efectivo',
                    'requiere_referencia' => false,
                    'activo' => true,
                    'comision_porcentaje' => null,
                    'orden' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nombre' => 'Tarjeta de Crédito',
                    'descripcion' => 'Pago con tarjeta de crédito',
                    'requiere_referencia' => true,
                    'activo' => true,
                    'comision_porcentaje' => 3.50,
                    'orden' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nombre' => 'Tarjeta de Débito',
                    'descripcion' => 'Pago con tarjeta de débito',
                    'requiere_referencia' => true,
                    'activo' => true,
                    'comision_porcentaje' => 1.50,
                    'orden' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nombre' => 'Transferencia Bancaria',
                    'descripcion' => 'Transferencia o depósito bancario',
                    'requiere_referencia' => true,
                    'activo' => true,
                    'comision_porcentaje' => null,
                    'orden' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nombre' => 'Crédito',
                    'descripcion' => 'Venta a crédito',
                    'requiere_referencia' => false,
                    'activo' => true,
                    'comision_porcentaje' => null,
                    'orden' => 5,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodos_pago');
    }
};