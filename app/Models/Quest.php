<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Screen\AsSource;

class Quest extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'game_id', // <-- This is the foreign key
        'name',
        'price',
        'difficulty'
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
