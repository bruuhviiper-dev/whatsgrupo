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
        Schema::create('figurinhas', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('titulo', 60);
            $table->string('slug')->unique();
            $table->string('arquivo_path');
            $table->string('arquivo_original');
            $table->string('categoria');
            $table->json('tags')->nullable();
            $table->unsignedInteger('downloads')->default(0);
            $table->unsignedInteger('visualizacoes')->default(0);
            $table->string('status')->default('pendente');
            $table->text('motivo_rejeicao')->nullable();
            $table->foreignUlid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_envio');
            $table->timestamp('aprovado_em')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('figurinhas');
    }
};
