<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'text',
        'price',
        'category',
        'brand',
        'color',
        'image',
        'stock',
        'min_stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->where('approved', true);
    }

    public function isLowStock()
    {
        return $this->stock <= ($this->min_stock ?? 5);
    }

    public function isOutOfStock()
    {
        return $this->stock <= 0;
    }
}
