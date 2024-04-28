<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;

class ServiceOption extends Model
{
    use HasFactory, AsSource;

    protected $fillable = [
        'name',
        'price',
        'parent_id',
        'type',
        'service',
    ];

    public function parent()
    {
        return $this->belongsTo(ServiceOption::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ServiceOption::class, 'parent_id');
    }

    public function isRadio()
    {
        return $this->type === 'radio';
    }

    public function isCheckbox()
    {
        return $this->type === 'checkbox';
    }

    public function scopeService($query, $service)
    {
        return $query->where('service', $service);
    }
}
