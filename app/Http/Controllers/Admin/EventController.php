<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        $eventos = Event::query()
            ->with('tipo')
            ->withCount(['registrations as inscritos' => fn ($q) => $q->where('status', 'confirmada')])
            ->orderByDesc('starts_at')
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'titulo' => $e->title,
                'clase' => $e->kind === 'evento_tienda' ? 'Evento de tienda' : 'Reserva de espacio',
                'tipo' => $e->tipo?->name,
                'fecha' => $e->starts_at->translatedFormat('d M Y'),
                'horas' => $e->starts_at->format('H:i').'–'.$e->ends_at->format('H:i'),
                'aforo' => $e->capacity,
                'inscritos' => $e->inscritos,
                'estado' => $e->status,
            ]);

        return Inertia::render('Admin/Eventos/Index', ['eventos' => $eventos]);
    }
}