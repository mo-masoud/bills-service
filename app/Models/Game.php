<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

/**
 * @property mixed $name
 */
class Game extends Model
{
    use HasFactory, SoftDeletes, AsSource, Filterable, Searchable;

    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public function powerlevel(): HasOne
    {
        return $this->hasOne(PowerLevel::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(SkillSection::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class);
    }

    public function quests(): HasMany
    {
        return $this->hasMany(Quest::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
