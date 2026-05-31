<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('is_gambling')->default(false)->after('status')
                ->comment('Tag de apostas/bet — visível só no dashboard. Impede impulsionamento e não aparece no site público.');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('is_gambling');
        });
    }
};
