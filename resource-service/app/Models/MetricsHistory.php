<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetricsHistory extends Model
{
    protected $table = 'metrics_history';

    protected $fillable = [
        'cpu_usage',
        'memory_usage',
        'disk_usage',
        'recorded_at',
    ];

    protected $casts = [
        'cpu_usage' => 'decimal:2',
        'memory_usage' => 'decimal:2',
        'disk_usage' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];
}

