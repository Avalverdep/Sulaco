<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'producto' => $this->producto->name,
            'unidades' => $this->quantity,
            'estado' => $this->status,
            'caduca' => $this->expires_at?->format('d/m/Y'),
            'pendiente' => $this->producto->status === 'pedido',
            'posicion' => $this->when(
                $this->status === 'activa',
                fn () => $this->posicionEnCola()
            ),
            'unidades_antes' => $this->when(
                $this->status === 'activa',
                fn () => $this->unidadesPorDelante()
            ),
        ];
    }
}