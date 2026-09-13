<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventRegistrationController extends Controller
{
    public function store(Request $request, Event $evento)
    {
        if ($evento->kind !== 'evento_tienda' || $evento->status !== 'aprobado') {
            return back()->withErrors(['inscripcion' => 'Este evento no admite inscripciones.']);
        }

        if ($evento->ends_at->isPast()) {
            return back()->withErrors(['inscripcion' => 'Este evento ya ha terminado.']);
        }

        try {
            DB::transaction(function () use ($request, $evento) {
                $bloqueado = Event::where('id', $evento->id)->lockForUpdate()->first();

                $inscritos = EventRegistration::where('event_id', $bloqueado->id)
                    ->where('status', 'confirmada')
                    ->count();

                if ($inscritos >= $bloqueado->capacity) {
                    throw new \RuntimeException('completo');
                }

                EventRegistration::updateOrCreate(
                    ['event_id' => $bloqueado->id, 'user_id' => $request->user()->id],
                    ['status' => 'confirmada']
                );
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['inscripcion' => 'Ya no quedan plazas libres.']);
        }

        return back()->with('exito', 'Plaza reservada. Te esperamos en Sulaco.');
    }

    public function destroy(Request $request, Event $evento)
    {
        $inscripcion = EventRegistration::where('event_id', $evento->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'confirmada')
            ->first();

        if (! $inscripcion) {
            return back()->withErrors(['inscripcion' => 'No estabas inscrito en este evento.']);
        }

        $inscripcion->update(['status' => 'cancelada']);

        // PENDIENTE: avisar por email a quienes esperan hueco.

        return back()->with('exito', 'Has cancelado tu inscripción.');
    }
}