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
        Schema::create('login_history', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->string('device_name')->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('platform', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->enum('login_status', ['success', 'failed', 'blocked']);
            $table->string('failure_reason')->nullable();
            $table->boolean('is_2fa_used')->default(false);
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->integer('session_duration')->nullable();
            
            $table->index(['user_id', 'login_at']);
            $table->index('ip_address');
        });
        
        // Add foreign key constraint using raw SQL
        DB::statement('ALTER TABLE login_history ADD CONSTRAINT login_history_user_id_foreign FOREIGN KEY (user_id) REFERENCES usuarios(id_usuario) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_history');
    }
};
