<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class SkillRange extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'min',
        'max',
        'gp_xp',
        'skill_id'
    ];

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }
}
