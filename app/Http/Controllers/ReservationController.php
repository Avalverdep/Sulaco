<?php

namespace App\Http\Controllers;

use App\Actions\CancelReservation;
use App\Actions\ReserveProduct;
use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Http\Request;
use RuntimeException;

class ReservationController extends Controller
{
    public function store(Request $request, Product $producto, ReserveProduct $reservar)
    {
        $datos = $request->validate([
            'cantidad' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        try {
            $reservar->ejecutar($producto, $request->user(), $datos['cantidad']);
        } catch (RuntimeException $e) {
            return back()->withErrors(['reserva' => $e->getMessage()]);
        }

        return back()->with('exito', 'Reserva hecha. Te esperamos en la tienda.');
    }

    public function destroy(Request $request, Reservation $reserva, CancelReservation $cancelar)
    {
        abort_unless($reserva->user_id === $request->user()->id, 404);

        try {
            $cancelar->ejecutar($reserva, $request->user()->id);
        } catch (RuntimeException $e) {
            return back()->withErrors(['reserva' => $e->getMessage()]);
        }

        return back()->with('exito', 'Reserva cancelada.');
    }
}