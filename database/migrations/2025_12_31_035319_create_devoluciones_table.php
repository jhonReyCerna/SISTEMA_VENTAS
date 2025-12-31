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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id('id_devolucion');
            $table->string('numero_devolucion', 20)->unique();
            $table->integer('venta_id'); // Venta original
            $table->integer('cliente_id'); // Cliente que devuelve
            $table->date('fecha_devolucion');
            $table->enum('tipo_devolucion', ['TOTAL', 'PARCIAL']);
            $table->enum('motivo', ['DEFECTUOSO', 'NO_CONFORME', 'CAMBIO_MENTE', 'ERROR_VENDEDOR', 'GARANTIA', 'OTRO']);
            $table->text('descripcion_motivo')->nullable();
            $table->decimal('monto_devuelto', 10, 2);
            $table->enum('estado', ['PENDIENTE', 'APROBADO', 'RECHAZADO', 'PROCESADO'])->default('PENDIENTE');
            $table->enum('accion_tomada', ['REEMBOLSO', 'CAMBIO', 'CREDITO_TIENDA', 'REPARACION'])->nullable();
            $table->text('notas_internas')->nullable();
            $table->integer('procesado_por')->nullable(); // Usuario que procesó
            $table->timestamp('fecha_procesado')->nullable();
            $table->integer('creado_por'); // Usuario que registró
            $table->timestamps();
            
            $table->index(['venta_id']);
            $table->index(['cliente_id', 'fecha_devolucion']);
            $table->index(['estado', 'fecha_devolucion']);
            $table->index('numero_devolucion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
