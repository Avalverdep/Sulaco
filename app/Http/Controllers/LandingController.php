<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;

class LandingController extends Controller
{
    public function index()
    {
        $eventos = Event::query()
            ->with('tipo')
            ->where('kind', 'evento_tienda')
            ->where('status', 'aprobado')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->take(4)
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'titulo' => $e->title,
                'tipo' => $e->tipo?->name,
                'dia' => $e->starts_at->format('d'),
                'mes' => $e->starts_at->translatedFormat('M'),
                'hora' => $e->starts_at->format('H:i'),
                'plazas' => $e->capacity,
            ]);

        return Inertia::render('Home', [
            'eventos' => $eventos,
        ]);
    }
}