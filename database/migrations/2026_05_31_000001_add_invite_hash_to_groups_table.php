<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adiciona a coluna invite_hash à tabela groups para garantir unicidade
     * pela hash do convite, independente de variações de URL (/invite/, /join/, etc).
     */
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->string('invite_hash', 64)->nullable()->after('whatsapp_link');
        });

        // Popula a coluna invite_hash para todos os registros existentes
        DB::table('groups')->get()->each(function ($group) {
            $hash = self::extractHash($group->whatsapp_link);
            if ($hash) {
                DB::table('groups')
                    ->where('id', $group->id)
                    ->update(['invite_hash' => $hash]);
            }
        });

        // Adiciona índice único após popular os dados (ignora nulls nativamente no SQLite/MySQL)
        Schema::table('groups', function (Blueprint $table) {
            $table->unique('invite_hash');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropUnique(['invite_hash']);
            $table->dropColumn('invite_hash');
        });
    }

    /**
     * Extrai a hash/código de um link do WhatsApp, normalizando qualquer variação de URL.
     */
    private static function extractHash(string $link): ?string
    {
        // Grupos: chat.whatsapp.com com /invite/, /join/, /v/, /v= ou diretamente a hash
        if (preg_match('/chat\.whatsapp\.com\/(?:invite\/|join\/|v\/|v=)?([a-zA-Z0-9_-]{10,})/i', $link, $matches)) {
            return $matches[1];
        }

        // Canais: whatsapp.com/channel/
        if (preg_match('/whatsapp\.com\/channel\/([a-zA-Z0-9@_-]{10,})/i', $link, $matches)) {
            return 'channel_' . $matches[1];
        }

        return null;
    }
};
