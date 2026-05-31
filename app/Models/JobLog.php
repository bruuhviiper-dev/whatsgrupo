<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model representando o log de execução das tarefas e coletores automáticos.
 */
class JobLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_type',
        'status',
        'result_summary',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
    ];
}
