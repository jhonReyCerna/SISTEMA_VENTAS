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
        Schema::create('detalle_devoluciones', function (Blueprint $table) {
            $table->id('id_detalle_devolucion');
            $table->integer('devolucion_id'); // Devolución asociada
            $table->integer('producto_id'); // Producto devuelto
            $table->integer('cantidad_devuelta'); // Cantidad devuelta
            $table->decimal('precio_unitario', 8, 2); // Precio unitario original
            $table->decimal('subtotal', 10, 2); // Subtotal de la devolución
            $table->text('observaciones')->nullable(); // Estado del producto devuelto
            $table->enum('condicion_producto', ['NUEVO', 'USADO_BUENO', 'USADO_REGULAR', 'DAÑADO']); // Condición
            $table->boolean('retornar_inventario')->default(true); // Si devolver al stock
            $table->timestamps();
            
            $table->index(['devolucion_id']);
            $table->index(['producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_devoluciones');
    }
};
