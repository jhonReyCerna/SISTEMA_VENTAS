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
ALTER TABLE usuarios 
ADD CONSTRAINT fk_usuarios_rol 
FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('ALTER TABLE usuarios DROP FOREIGN KEY fk_usuarios_rol');
    }
};
