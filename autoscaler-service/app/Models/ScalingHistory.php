<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScalingHistory extends Model
{
    protected $table = 'scaling_history';

    protected $fillable = [
        'action',
        'resource_id',
        'resource_name',
        'metrics',
        'reason',
    ];

    protected $casts = [
        'metrics' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

