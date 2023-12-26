<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Orchid\Screen\AsSource;

class Coupon extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'code',
        'number_of_uses',
        'number_of_used',
        'discount_percentage',
        'maximum_discount_amount',
        'valid_to',
    ];

    protected $casts = [
        'valid_to' => 'datetime',
    ];

    public function scopeValid($query)
    {
        return $query->where('valid_to', '>=', now());
    }

    public function scopeAvailable($query)
    {
        return $query->where('number_of_uses', '>', DB::raw('number_of_used'));
    }

    public function scopeValidAndAvailable($query)
    {
        return $query->available()->valid();
    }
}
