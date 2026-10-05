<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

class ExpireReservations extends Command
{
    protected $signature = 'reservas:caducar';
    protected $description = 'Caduca las reservas vencidas y devuelve su stock';
    
    public function handle(CancelReservation $cancelar): int
    {
        $vencidas = Reservation::where('status', 'activa')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($vencidas as $reserva) {
            try {
                $cancelar->ejecutar($reserva, null, 'caducada');
                $this->info("Caducada reserva #{$reserva->id}");
            } catch (\Throwable $e) {
                $this->error("Error en reserva #{$reserva->id}: {$e->getMessage()}");
            }
        }

        $this->info("Total: {$vencidas->count()}");

        return self::SUCCESS;
    }
}
