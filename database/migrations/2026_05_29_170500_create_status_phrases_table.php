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
        Schema::create('status_phrases', function (Blueprint $table) {
            $table->id();
            $table->text('phrase');
            $table->string('author')->nullable();
            $table->string('category')->index(); // amor, amizade, motivacao, engracado, reflexao
            $table->integer('likes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_phrases');
    }
};
