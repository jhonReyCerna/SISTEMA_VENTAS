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
CREATE TABLE movimientos_stock (
    id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT,
    tipo_movimiento ENUM('ENTRADA','SALIDA') NOT NULL,
    cantidad INT NOT NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    motivo VARCHAR(150)
)
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TABLE IF EXISTS movimientos_stock');
    }
};
