<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'image',
        'description',
        'levels',
        'price',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
