<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPowerLevelItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'game_id',
        'skill_id',
        'boot_method_id',
        'current_level',
        'desired_level',
        'price',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function bootMethod(): BelongsTo
    {
        return $this->belongsTo(BootMethod::class);
    }
}
