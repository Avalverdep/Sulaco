<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'quantity',
        'status', 'expires_at', 'collected_at', 'admin_note',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'collected_at' => 'datetime',
    ];

    public function producto()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posicionEnCola(): int
    {
        return static::where('product_id', $this->product_id)
            ->where('status', 'activa')
            ->where('created_at', '<', $this->created_at)
            ->count() + 1;
    }

    public function unidadesPorDelante(): int
    {
        return static::where('product_id', $this->product_id)
            ->where('status', 'activa')
            ->where('created_at', '<', $this->created_at)
            ->sum('quantity');
    }
}