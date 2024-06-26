<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Order extends Model
{
    use HasFactory, AsSource, Filterable, Searchable;

    protected $fillable = [
        'user_id',
        'original_price',
        'discount_price',
        'total_price',
        'status', // pending, canceled, failed and completed
        'cancellation_reason',
    ];

    public function skillItems(): HasMany
    {
        return $this->hasMany(OrderSkillItem::class);
    }

    public function questItems(): HasMany
    {
        return $this->hasMany(QuestOrderItem::class);
    }

    public function serviceItems(): HasMany
    {
        return $this->hasMany(ServiceOrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
