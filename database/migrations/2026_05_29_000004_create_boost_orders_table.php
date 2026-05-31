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
        Schema::create('boost_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boost_package_id')->constrained('boost_packages')->onDelete('cascade');
            $table->string('buyer_name');
            $table->string('buyer_email');
            $table->enum('payment_method', ['pix', 'card']);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_id')->nullable(); // ID externo retornado pela Efí Bank
            $table->text('pix_qr_code')->nullable(); // Imagem base64 do QR Code para pagamentos via PIX
            $table->text('pix_copy_paste')->nullable(); // Linha digitável (copia e cola) do PIX
            $table->string('boost_code', 12)->unique()->nullable(); // Código de impulso gerado pós-pagamento
            $table->integer('boosts_total');
            $table->integer('boosts_used')->default(0);
            $table->decimal('amount', 8, 2);
            $table->timestamps();

            // Índices para buscas rápidas
            $table->index('payment_status');
            $table->index('boost_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boost_orders');
    }
};
