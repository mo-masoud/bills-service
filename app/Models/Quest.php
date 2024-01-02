<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quest extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id', // <-- This is the foreign key
        'name',
        'difficulty',
        'price',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
