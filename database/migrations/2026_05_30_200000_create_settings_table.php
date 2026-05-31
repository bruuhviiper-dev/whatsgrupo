<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // Valores padrão
        DB::table('settings')->insert([
            ['key' => 'adsense_client_id',     'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'adsense_script',        'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'adsense_meta_tag',      'value' => null, 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'adsense_enabled',       'value' => '0',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'adsense_slot_auto',     'value' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
