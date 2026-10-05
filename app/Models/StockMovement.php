<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'type', 'quantity', 'note',
        'origen_type', 'origen_id',
    ];

    public function producto()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function origen()
    {
        return $this->morphTo();
    }
}