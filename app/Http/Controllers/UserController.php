<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Resources\ReservationResource;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $reservas = ReservationResource::collection(
            Reservation::with('producto:id,name,status')
                ->where('user_id', $usuario->id)
                ->whereIn('status', ['activa', 'recogida'])
                ->orderByDesc('created_at')
                ->get()
        )->resolve();

        $inscripciones = $usuario->inscripciones()
            ->with('event:id,title,starts_at,ends_at')
            ->where('status', 'confirmada')
            ->get()
            ->filter(fn ($i) => $i->event && $i->event->ends_at->isFuture())
            ->map(fn ($i) => [
                'id' => $i->id,
                'titulo' => $i->event->title,
                'dia' => $i->event->starts_at->format('d'),
                'mes' => $i->event->starts_at->translatedFormat('M'),
                'horas' => $i->event->starts_at->format('H:i').'–'.$i->event->ends_at->format('H:i'),
            ])
            ->values();

        return Inertia::render('User/Index', [
            'reservas' => $reservas,
            'inscripciones' => $inscripciones,
        ]);
    }
}
