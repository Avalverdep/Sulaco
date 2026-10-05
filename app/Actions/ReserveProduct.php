<?php

namespace App\Actions;

use App\Models\Product;
use App\Models\Reservation;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReserveProduct
{
    public function ejecutar(Product $producto, User $usuario, int $cantidad): Reservation
    {
        return DB::transaction(function () use ($producto, $usuario, $cantidad) {
            $bloqueado = Product::whereKey($producto->id)->lockForUpdate()->first();

            if (! in_array($bloqueado->status, ['disponible', 'pedido'])) {
                throw new RuntimeException('Este producto ya no está disponible.');
            }

            if ($bloqueado->stock < $cantidad) {
                throw new RuntimeException('No quedan suficientes unidades.');
            }

            $yaReservadas = Reservation::where('product_id', $bloqueado->id)
                ->where('user_id', $usuario->id)
                ->where('status', 'activa')
                ->sum('quantity');

            if ($yaReservadas + $cantidad > $bloqueado->max_per_user) {
                throw new RuntimeException(
                    "Solo puedes reservar {$bloqueado->max_per_user} unidades de este producto."
                );
            }

            $bloqueado->decrement('stock', $cantidad);

            $reserva = Reservation::create([
                'product_id' => $bloqueado->id,
                'user_id' => $usuario->id,
                'quantity' => $cantidad,
                'status' => 'activa',
                'expires_at' => now()->addDays($bloqueado->reservation_days),
            ]);

            StockMovement::create([ 
                'product_id' => $bloqueado->id,
                'user_id' => $usuario->id,
                'type' => 'reserva',
                'quantity' => -$cantidad,
                'origen_type' => Reservation::class,
                'origen_id' => $reserva->id,
            ]);

            return $reserva;
        });
    }
}