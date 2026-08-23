<?php

namespace App\Rules;

use App\Models\Event;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SinSolapamiento implements ValidationRule
{
    public function __construct(
        private ?string $starts_at,
        private ?int $ignorarId = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->starts_at || ! $value) {
            return;
        }

        $existe = Event::query()
            ->whereIn('status', ['pendiente', 'aprobado'])
            ->where('starts_at', '<', $value)
            ->where('ends_at', '>', $this->starts_at)
            ->when($this->ignorarId, fn ($q) => $q->where('id', '!=', $this->ignorarId))
            ->exists();

        if ($existe) {
            $fail('Ya hay algo programado en esa franja horaria.');
        }
    }
}