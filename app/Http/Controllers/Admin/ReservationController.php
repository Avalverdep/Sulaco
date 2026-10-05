<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CancelReservation;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use RuntimeException;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->string('estado')->toString() ?: 'activa';

        $reservas = Reservation::query()
            ->with(['producto:id,name', 'user:id,name,email'])
            ->when($estado !== 'todas', fn ($q) => $q->where('status', $estado))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'producto' => $r->producto->name,
                'cliente' => $r->user->name,
                'email' => $r->user->email,
                'unidades' => $r->quantity,
                'estado' => $r->status,
                'creada' => $r->created_at->format('d/m/Y'),
                'caduca' => $r->expires_at?->format('d/m/Y'),
                'vencida' => $r->status === 'activa' && $r->expires_at?->isPast(),
            ]);

        return Inertia::render('Admin/Reservas/Index', [
            'reservas' => $reservas,
            'estado' => $estado,
            'conteos' => Reservation::selectRaw('status, count(*) as total')
                ->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function collect(Reservation $reserva)
    {
        if ($reserva->status !== 'activa') {
            return back()->withErrors(['reserva' => 'Esta reserva ya no está activa.']);
        }

        $reserva->update(['status' => 'recogida', 'collected_at' => now()]);

        // PENDIENTE: generar la venta con sus líneas e IVA.

        return back()->with('exito', 'Reserva marcada como recogida.');
    }

    public function cancel(Reservation $reserva, CancelReservation $cancelar, Request $request)
    {
        try {
            $cancelar->ejecutar($reserva, $request->user()->id);
        } catch (RuntimeException $e) {
            return back()->withErrors(['reserva' => $e->getMessage()]);
        }

        return back()->with('exito', 'Reserva cancelada y stock devuelto.');
    }
}