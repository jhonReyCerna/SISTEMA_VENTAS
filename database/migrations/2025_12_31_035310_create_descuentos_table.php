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
        Schema::create('descuentos', function (Blueprint $table) {
            $table->id('id_descuento');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['PORCENTAJE', 'MONTO_FIJO']); // Tipo de descuento
            $table->decimal('valor', 8, 2); // Porcentaje o monto fijo
            $table->enum('aplicacion', ['CLIENTE', 'PRODUCTO', 'CATEGORIA', 'GENERAL']); // A qué se aplica
            $table->integer('cliente_id')->nullable(); // Si es específico para un cliente
            $table->integer('producto_id')->nullable(); // Si es específico para un producto
            $table->integer('categoria_id')->nullable(); // Si es específico para una categoría
            $table->decimal('monto_minimo', 10, 2)->nullable(); // Monto mínimo para aplicar
            $table->integer('cantidad_minima')->nullable(); // Cantidad mínima para aplicar
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->integer('usos_maximos')->nullable(); // Límite de usos
            $table->integer('usos_actuales')->default(0); // Usos registrados
            $table->timestamps();
            
            $table->index(['cliente_id', 'activo']);
            $table->index(['producto_id', 'activo']);
            $table->index(['categoria_id', 'activo']);
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('descuentos');
    }
};
