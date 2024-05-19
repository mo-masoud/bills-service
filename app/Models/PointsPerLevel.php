<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointsPerLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'points',
    ];
}
