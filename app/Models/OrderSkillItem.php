<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSkillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'skill_id',
        'min_level',
        'max_level',
        'boost_method_id',
        'express',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function boostMethod()
    {
        return $this->belongsTo(BootMethod::class, 'boost_method_id');
    }
}
