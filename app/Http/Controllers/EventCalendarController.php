<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventCalendarController extends Controller
{
    public function index(Request $request)
    {
        $inicio = $request->date('semana')?->startOfWeek() ?? now()->startOfWeek();
        $fin = $inicio->copy()->endOfWeek();

        $eventos = Event::query()
            ->with('tipo')
            ->withCount(['registrations as inscritos' => fn ($q) => $q->where('status', 'confirmada')])
            ->where('status', 'aprobado')
            ->whereBetween('starts_at', [$inicio, $fin])
            ->orderBy('starts_at')
            ->get()
            ->map(function ($e) {
                $libres = $e->capacity ? max($e->capacity - $e->inscritos, 0) : null;

                return [
                    'id' => $e->id,
                    'titulo' => $e->kind === 'reserva_usuario' ? 'Mesa reservada' : $e->title,
                    'fecha' => $e->starts_at->toDateString(),
                    'horas' => $e->starts_at->format('H:i').'–'.$e->ends_at->format('H:i'),
                    'horaInicio' => $e->starts_at->hour + $e->starts_at->minute / 60,
                    'horaFin' => $e->ends_at->hour + $e->ends_at->minute / 60,
                    'aforo' => $e->capacity,
                    'libres' => $libres,
                    'completo' => $libres !== null && $libres <= 0,
                ];
            });

        $dias = collect(range(0, 6))->map(function ($i) use ($inicio) {
            $dia = $inicio->copy()->addDays($i);

            return [
                'fecha' => $dia->toDateString(),
                'nombre' => $dia->translatedFormat('D'),
                'numero' => $dia->day,
                'esHoy' => $dia->isToday(),
            ];
        });

        return Inertia::render('Eventos', [
            'eventos' => $eventos,
            'dias' => $dias,
            'semanaActual' => $inicio->toDateString(),
            'semanaAnterior' => $inicio->copy()->subWeek()->toDateString(),
            'semanaSiguiente' => $inicio->copy()->addWeek()->toDateString(),
            'rotulo' => $inicio->translatedFormat('j M').' – '.$fin->translatedFormat('j M Y'),
        ]);
    }

        public function show(Request $request, Event $evento)
    {
        abort_unless(in_array($evento->status, ['aprobado', 'pendiente']), 404);

        $inscritos = $evento->registrations()->where('status', 'confirmada')->count();
        $libres = $evento->capacity ? max($evento->capacity - $inscritos, 0) : null;
        $esPrivado = $evento->kind === 'reserva_usuario';

                $inscrito = $request->user()
            ? $evento->registrations()
                ->where('user_id', $request->user()->id)
                ->where('status', 'confirmada')
                ->exists()
            : false;

        return response()->json([
            'id' => $evento->id,
            'titulo' => $esPrivado ? 'Mesa reservada' : $evento->title,
            'descripcion' => $esPrivado ? null : $evento->description,
            'tipo' => $esPrivado ? null : $evento->tipo?->name,
            'privado' => $esPrivado,
            'fecha' => $evento->starts_at->translatedFormat('l, j \d\e F'),
            'horas' => $evento->starts_at->format('H:i').'–'.$evento->ends_at->format('H:i'),
            'aforo' => $evento->capacity,
            'libres' => $libres,
            'completo' => $libres !== null && $libres <= 0,
            'pasado' => $evento->ends_at->isPast(),
            'inscrito' => $inscrito,
        ]);
    }
}
