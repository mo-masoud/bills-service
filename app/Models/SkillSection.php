<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkillSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'name',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class, 'section_id');
    }
}
