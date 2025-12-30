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
ALTER TABLE ventas 
ADD CONSTRAINT fk_ventas_cliente 
FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
SQL);

        DB::unprepared(<<<SQL
ALTER TABLE ventas 
ADD CONSTRAINT fk_ventas_usuario 
FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('ALTER TABLE ventas DROP FOREIGN KEY fk_ventas_cliente');
        DB::unprepared('ALTER TABLE ventas DROP FOREIGN KEY fk_ventas_usuario');
    }
};
