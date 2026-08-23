<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest{

   public function authorize(): bool{
    return true;
    }

    public function rules(): array
    {
        return [
            'kind' => ['required', 'in:evento_tienda,reserva_usuario'],
            'event_type_id' => ['nullable', 'required_if:kind,evento_tienda', 'exists:event_types,id'],
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:2000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => [
            'required', 'date', 'after:starts_at',
            new \App\Rules\SinSolapamiento($this->input('starts_at')),
            ],
            'capacity' => ['nullable', 'required_if:kind,evento_tienda', 'integer', 'min:1', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'ends_at.after' => 'La hora de fin debe ser posterior a la de inicio.',
            'event_type_id.required_if' => 'Selecciona el tipo de evento.',
            'capacity.required_if' => 'Indica cuántas plazas hay.',
        ];
    }
}
