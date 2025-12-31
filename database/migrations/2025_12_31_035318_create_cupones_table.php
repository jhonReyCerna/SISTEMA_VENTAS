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
        Schema::create('cupones', function (Blueprint $table) {
            $table->id('id_cupon');
            $table->string('codigo', 20)->unique(); // Código del cupón
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->enum('tipo_descuento', ['PORCENTAJE', 'MONTO_FIJO']);
            $table->decimal('valor_descuento', 8, 2);
            $table->decimal('monto_minimo', 10, 2)->nullable(); // Compra mínima
            $table->decimal('descuento_maximo', 10, 2)->nullable(); // Descuento máximo
            $table->date('fecha_inicio');
            $table->date('fecha_expiracion');
            $table->integer('usos_maximos')->nullable(); // -1 para ilimitado
            $table->integer('usos_por_cliente')->default(1); // Usos por cliente
            $table->integer('usos_actuales')->default(0);
            $table->boolean('activo')->default(true);
            $table->boolean('primera_compra')->default(false); // Solo primera compra
            $table->json('productos_incluidos')->nullable(); // IDs de productos específicos
            $table->json('categorias_incluidas')->nullable(); // IDs de categorías específicas
            $table->json('clientes_incluidos')->nullable(); // IDs de clientes específicos
            $table->integer('creado_por'); // Usuario que creó el cupón
            $table->timestamps();
            
            $table->index(['codigo', 'activo']);
            $table->index(['fecha_inicio', 'fecha_expiracion']);
            $table->index('creado_por');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cupones');
    }
};
