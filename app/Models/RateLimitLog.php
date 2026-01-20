<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateLimitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'count',
        'reset_at',
    ];

    protected $casts = [
        'reset_at' => 'datetime',
    ];
}
