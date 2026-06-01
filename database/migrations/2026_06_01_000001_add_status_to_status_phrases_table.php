<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('status_phrases', function (Blueprint $table) {
            // 'aprovado' como default mantém todas as frases seedadas visíveis
            $table->string('status')->default('aprovado')->after('likes');
            $table->text('motivo_rejeicao')->nullable()->after('status');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('status_phrases', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'motivo_rejeicao']);
        });
    }
};
