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
        Schema::table('ventas', function (Blueprint $table) {
            // Campos para descuentos y cupones
            $table->integer('descuento_id')->nullable()->after('total');
            $table->integer('cupon_id')->nullable()->after('descuento_id');
            $table->decimal('descuento_aplicado', 8, 2)->default(0)->after('cupon_id');
            $table->decimal('subtotal_antes_descuento', 10, 2)->nullable()->after('descuento_aplicado');
            
            // Campo para método de pago
            $table->integer('metodo_pago_id')->nullable()->after('subtotal_antes_descuento');
            $table->string('referencia_pago', 100)->nullable()->after('metodo_pago_id');
            
            // Campos para crédito
            $table->boolean('es_credito')->default(false)->after('referencia_pago');
            $table->integer('dias_credito')->nullable()->after('es_credito');
            $table->date('fecha_vencimiento_credito')->nullable()->after('dias_credito');
            
            // Campo para comisión del método de pago
            $table->decimal('comision_pago', 8, 2)->default(0)->after('fecha_vencimiento_credito');
            
            // Índices para optimización
            $table->index(['descuento_id']);
            $table->index(['cupon_id']);
            $table->index(['metodo_pago_id']);
            $table->index(['es_credito', 'fecha_vencimiento_credito']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex(['descuento_id']);
            $table->dropIndex(['cupon_id']);
            $table->dropIndex(['metodo_pago_id']);
            $table->dropIndex(['es_credito', 'fecha_vencimiento_credito']);
            
            $table->dropColumn([
                'descuento_id',
                'cupon_id', 
                'descuento_aplicado',
                'subtotal_antes_descuento',
                'metodo_pago_id',
                'referencia_pago',
                'es_credito',
                'dias_credito',
                'fecha_vencimiento_credito',
                'comision_pago'
            ]);
        });
    }
};
