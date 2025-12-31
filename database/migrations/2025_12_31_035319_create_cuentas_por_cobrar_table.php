<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cuentas_por_cobrar', function (Blueprint $table) {
            $table->id('id_cuenta_por_cobrar');
            $table->integer('cliente_id'); // Cliente al que se le debe
            $table->integer('venta_id'); // Venta asociada
            $table->decimal('monto_total', 10, 2); // Monto total de la deuda
            $table->decimal('monto_pendiente', 10, 2); // Monto pendiente de pago
            $table->decimal('monto_pagado', 10, 2)->default(0); // Monto ya pagado
            $table->date('fecha_vencimiento'); // Fecha límite de pago
            $table->enum('estado', ['PENDIENTE', 'PARCIAL', 'PAGADO', 'VENCIDO'])->default('PENDIENTE');
            $table->text('notas')->nullable();
            $table->integer('dias_credito'); // Días de crédito otorgados
            $table->decimal('interes_moratorio', 5, 2)->default(0); // % de interés por mora
            $table->decimal('interes_acumulado', 10, 2)->default(0); // Interés acumulado
            $table->integer('creado_por'); // Usuario que registró
            $table->timestamps();
            
            $table->index(['cliente_id', 'estado']);
            $table->index(['venta_id']);
            $table->index(['fecha_vencimiento', 'estado']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuentas_por_cobrar');
    }
};
