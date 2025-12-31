<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('pagos_cuentas_por_cobrar', function (Blueprint $table) {
            $table->id('id_pago');
            $table->integer('cuenta_por_cobrar_id'); 
            $table->decimal('monto_pago', 10, 2);
            $table->date('fecha_pago'); 
            $table->integer('metodo_pago_id'); 
            $table->string('referencia', 100)->nullable(); 
            $table->text('notas')->nullable(); 
            $table->decimal('interes_pagado', 10, 2)->default(0); 
            $table->integer('registrado_por'); 
            $table->timestamps();
            
            $table->index(['cuenta_por_cobrar_id', 'fecha_pago']);
            $table->index('metodo_pago_id');
            $table->index('fecha_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_cuentas_por_cobrar');
    }
};
