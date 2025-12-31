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
        Schema::table('user_2fa', function (Blueprint $table) {
            // Aumentar la longitud del campo secret_key para Google 2FA
            $table->string('secret_key', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_2fa', function (Blueprint $table) {
            // Revertir al tamaño original
            $table->string('secret_key', 32)->nullable()->change();
        });
    }
};
