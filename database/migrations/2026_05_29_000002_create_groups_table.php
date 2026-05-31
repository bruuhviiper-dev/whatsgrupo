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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('rules')->nullable();
            $table->string('image_path')->nullable();
            $table->string('whatsapp_link')->unique();
            $table->string('submitter_email')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_vip')->default(false);
            $table->timestamp('vip_expires_at')->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->timestamps();

            // Índices para otimização de consultas
            $table->index('status');
            $table->index('is_vip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
