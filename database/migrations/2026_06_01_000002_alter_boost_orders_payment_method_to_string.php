<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Converte payment_method de enum('pix','card') para string,
 * permitindo novos métodos via Stripe como 'boleto' e 'gpay' (Google Pay).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boost_orders', function (Blueprint $table) {
            $table->string('payment_method', 20)->change();
        });
    }

    public function down(): void
    {
        Schema::table('boost_orders', function (Blueprint $table) {
            $table->enum('payment_method', ['pix', 'card'])->change();
        });
    }
};
