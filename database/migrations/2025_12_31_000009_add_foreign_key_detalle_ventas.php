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
        DB::unprepared(<<<SQL
ALTER TABLE detalle_ventas 
ADD CONSTRAINT fk_detalle_venta 
FOREIGN KEY (id_venta) REFERENCES ventas(id_venta)
SQL);

        DB::unprepared(<<<SQL
ALTER TABLE detalle_ventas 
ADD CONSTRAINT fk_detalle_producto 
FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('ALTER TABLE detalle_ventas DROP FOREIGN KEY fk_detalle_venta');
        DB::unprepared('ALTER TABLE detalle_ventas DROP FOREIGN KEY fk_detalle_producto');
    }
};
