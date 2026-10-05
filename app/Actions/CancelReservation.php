<?php

namespace App\Actions;

use App\Models\Product;
use App\Models\Reservation;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CancelReservation
{
    public function ejecutar(Reservation $reserva, ?int $usuarioId = null, string $motivo = 'cancelada'): Reservation
    {
        if ($reserva->status !== 'activa') {
            throw new RuntimeException('Esta reserva ya no está activa.');
        }

        return DB::transaction(function () use ($reserva, $usuarioId, $motivo) {
            $bloqueada = Reservation::whereKey($reserva->id)->lockForUpdate()->first();

            if ($bloqueada->status !== 'activa') {
                throw new RuntimeException('Esta reserva ya no está activa.');
            }

            Product::whereKey($bloqueada->product_id)->increment('stock', $bloqueada->quantity);

            $bloqueada->update(['status' => $motivo]);

            StockMovement::create([
                'product_id' => $bloqueada->product_id,
                'user_id' => $usuarioId,
                'type' => 'liberacion',
                'quantity' => $bloqueada->quantity,
                'origen_type' => Reservation::class,
                'origen_id' => $bloqueada->id,
                'note' => $motivo === 'caducada' ? 'Reserva caducada' : null,
            ]);

            return $bloqueada->fresh();
        });
    }
}