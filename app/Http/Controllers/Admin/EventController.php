<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\EventType;
use App\Http\Requests\StoreEventRequest;

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

    public function create() {
        return Inertia::render('Admin/Eventos/Create', [
            'tipos' => EventType::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreEventRequest $request) {
        $datos = $request->validated();

        Event::create([
            ...$datos,
            'created_by' => $request->user()->id,
            'status' => 'aprobado',
        ]);

        return redirect()
            ->route('admin.eventos.index')
            ->with('exito', 'Evento creado correctamente.');
    }

        public function destroy(Event $evento) {
        if ($evento->registrations()->exists()) {
            return back()->withErrors([
                'evento' => 'Este evento tiene inscritos. Cancélalo en vez de borrarlo.',
            ]);
        }

        $evento->delete();

        return redirect()->route('admin.eventos.index')->with('exito', 'Evento eliminado.');
    }

        public function cancel(Event $evento) {
        $evento->update(['status' => 'cancelado']);

        // PENDIENTE: notificar por email a los inscritos.

        return redirect()->route('admin.eventos.index')->with('exito', 'Evento cancelado.');
    }
}