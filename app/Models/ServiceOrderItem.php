<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceOrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_service_items';

    protected $fillable = [
        'service_option_id',
        'order_id',
        'checkboxes',
        'price',
    ];

    protected $casts = [
        'checkboxes' => 'array',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ServiceOption::class, 'service_option_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
