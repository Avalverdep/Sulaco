<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description', 'price',
        'stock', 'status', 'max_per_user', 'image_path',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function categoria()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function reservas()
    {
        return $this->hasMany(Reservation::class);
    }

    public function movimientos()
    {
        return $this->hasMany(StockMovement::class);
    }
}