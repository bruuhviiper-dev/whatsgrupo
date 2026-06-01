<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raffle_draws', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title')->nullable();          // Título opcional do sorteio
            $table->json('participants');                 // Array com todos os nomes
            $table->json('winners');                      // Array com os vencedores
            $table->integer('winner_count')->default(1);
            $table->string('mode')->default('random');   // roulette | random
            $table->integer('total_participants');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raffle_draws');
    }
};
