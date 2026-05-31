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
        Schema::create('group_analyses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('group_name');
            $table->string('category');
            
            // Dados da análise simulada
            $table->string('engagement_level'); // Baixo, Médio, Alto, Muito Alto
            $table->integer('engagement_percent'); // 0-100
            $table->integer('msgs_per_day');
            $table->string('peak_time');
            $table->string('growth_trend'); // Crescendo, Estável, Declinando
            $table->decimal('health_score', 3, 1); // 0.0 a 10.0
            
            // Arrays (JSON)
            $table->json('pros')->nullable();
            $table->json('cons')->nullable();
            
            // Textos
            $table->text('public_summary')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_analyses');
    }
};
