<?php

namespace App\Actions;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AdjustStock
{
    public function ajustar(Product $producto, int $delta): Product
    {
        return DB::transaction(function () use ($producto, $delta) {
            $bloqueado = Product::whereKey($producto->id)->lockForUpdate()->first();

            if ($bloqueado->stock + $delta < 0) {
                throw new InvalidArgumentException('El stock no puede quedar en negativo.');
            }

            $bloqueado->increment('stock', $delta);


            return $bloqueado->fresh();
        });
    }

    public function fijar(Product $producto, int $cantidad): Product
    {
        if ($cantidad < 0) {
            throw new InvalidArgumentException('El stock no puede ser negativo.');
        }

        return DB::transaction(function () use ($producto, $cantidad) {
            $bloqueado = Product::whereKey($producto->id)->lockForUpdate()->first();
            $bloqueado->update(['stock' => $cantidad]);


            return $bloqueado->fresh();
        });
    }
}