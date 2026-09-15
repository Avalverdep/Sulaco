<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999'],
            'stock' => ['required', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', 'in:pedido,disponible,descatalogado'],
            'max_per_user' => ['required', 'integer', 'min:1', 'max:50'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'imagen.max' => 'La imagen no puede pesar más de 5 MB.',
            'imagen.mimes' => 'Sube una imagen JPG, PNG o WebP.',
            'category_id.required' => 'Elige una categoría.',
        ];
    }
}
