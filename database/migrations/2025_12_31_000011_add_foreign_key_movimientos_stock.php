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
ALTER TABLE movimientos_stock 
ADD CONSTRAINT fk_movimiento_producto 
FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('ALTER TABLE movimientos_stock DROP FOREIGN KEY fk_movimiento_producto');
    }
};
