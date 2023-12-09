<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Screen\AsSource;

class Skill extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'game_id',
        'name',
        'section_id'
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(SkillSection::class, 'section_id');
    }

    public function bootMethods(): HasMany
    {
        return $this->hasMany(BootMethod::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(SkillPrice::class);
    }
}
