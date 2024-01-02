<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
        'price',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
