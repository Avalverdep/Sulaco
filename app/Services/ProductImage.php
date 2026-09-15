<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Encoders\WebpEncoder;

class ProductImage
{
    private const TAMANO = 1000;
    private const MINIATURA = 400;

    public function guardar(UploadedFile $archivo): string
    {
        $nombre = Str::uuid().'.webp';
        $imagen = Image::decode($archivo);

        Storage::disk('public')->put(
            "products/{$nombre}",
            (string) $imagen->cover(self::TAMANO, self::TAMANO)->encode(new WebpEncoder(85))
        );

        Storage::disk('public')->put(
            "products/thumbs/{$nombre}",
            (string) $imagen->cover(self::MINIATURA, self::MINIATURA)->encode(new WebpEncoder(80))
        );

        return $nombre;
    }

    public function borrar(?string $nombre): void
    {
        if (! $nombre) return;

        Storage::disk('public')->delete([
            "products/{$nombre}",
            "products/thumbs/{$nombre}",
        ]);
    }

    public static function url(?string $nombre, bool $miniatura = false): ?string
    {
        if (! $nombre) return null;

        $carpeta = $miniatura ? 'products/thumbs' : 'products';

        return Storage::disk('public')->url("{$carpeta}/{$nombre}");
    }
}