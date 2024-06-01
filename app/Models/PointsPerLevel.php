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

    public static function getPoints($minLevel, $maxLevel)
    {
        return self::where('level', '>', $minLevel)->where('level', '<=', $maxLevel)->sum('points');
    }
}
