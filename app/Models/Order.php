<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_price',
        'discount_price',
        'total_price',
        'status', // pending, canceled and completed
        'cancellation_reason',
    ];

    public function powerlevelItems(): HasMany
    {
        return $this->hasMany(OrderPowerLevelItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
