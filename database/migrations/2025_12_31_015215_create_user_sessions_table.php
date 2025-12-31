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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->integer('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->string('device_name')->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('platform', 100)->nullable();
            $table->boolean('is_current')->default(false);
            $table->integer('last_activity');
            $table->timestamp('created_at')->nullable();
            
            $table->index(['user_id', 'last_activity']);
        });
        
        // Add foreign key constraint using raw SQL
        DB::statement('ALTER TABLE user_sessions ADD CONSTRAINT user_sessions_user_id_foreign FOREIGN KEY (user_id) REFERENCES usuarios(id_usuario) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
