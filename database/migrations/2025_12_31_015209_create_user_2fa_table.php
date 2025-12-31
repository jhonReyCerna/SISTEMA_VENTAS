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
        // First, create table without foreign key
        Schema::create('user_2fa', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('secret_key', 32)->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->text('backup_codes')->nullable();
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
        });
        
        // Add foreign key constraint using raw SQL
        DB::statement('ALTER TABLE user_2fa ADD CONSTRAINT user_2fa_user_id_foreign FOREIGN KEY (user_id) REFERENCES usuarios(id_usuario) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_2fa');
    }
};
