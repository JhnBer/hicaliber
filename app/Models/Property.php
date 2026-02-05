<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'bedrooms',
        'bathrooms',
        'storeys',
        'garages',
    ];

    public function scopeSearch($query, array $filters)
    {
        return $query
            ->when($filters['name'] ?? null, function ($q, $name) {
                $q->where('name', 'like', '%' . $name . '%');
            })
            ->when($filters['bedrooms'] ?? null, fn($q, $v) => $q->where('bedrooms', $v))
            ->when($filters['bathrooms'] ?? null, fn($q, $v) => $q->where('bathrooms', $v))
            ->when($filters['storeys'] ?? null, fn($q, $v) => $q->where('storeys', $v))
            ->when($filters['garages'] ?? null, fn($q, $v) => $q->where('garages', $v))
            ->when($filters['min_price'] ?? null, fn($q, $v) => $q->where('price', '>=', $v))
            ->when($filters['max_price'] ?? null, fn($q, $v) => $q->where('price', '<=', $v));
    }
}
