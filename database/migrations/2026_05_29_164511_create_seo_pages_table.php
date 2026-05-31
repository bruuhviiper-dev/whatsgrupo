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
        Schema::create('seo_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('h1');
            $table->string('meta_description', 160);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('keyword');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('extra_term')->nullable();
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->integer('views')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_pages');
    }
};
