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
CREATE TABLE logs_actividad (
    id_log BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    accion VARCHAR(50) NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    datos_anteriores TEXT NULL,
    datos_nuevos TEXT NULL,
    ip VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    INDEX idx_modulo_accion (modulo, accion),
    INDEX idx_fecha_hora (fecha_hora)
)
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_actividad');
    }
};
