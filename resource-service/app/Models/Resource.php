<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'name',
        'type',
        'status',
        'capacity',
        'cpu_capacity',
        'memory_capacity',
        'storage_capacity',
        'region'
    ];

}
