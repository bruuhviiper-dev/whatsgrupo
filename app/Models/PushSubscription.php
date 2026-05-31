<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model representando a assinatura de Web Push de um usuário.
 */
class PushSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'endpoint',
        'public_key',
        'auth_token',
        'category_ids',
    ];

    protected $casts = [
        'category_ids' => 'array', // Cast automático do JSON para array PHP
    ];
}
