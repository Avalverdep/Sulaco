<?php

namespace App\Actions;

use App\Models\Product;
use App\Services\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateProduct
{
    public function __construct(private ProductImage $imagenes) {}

    public function ejecutar(Product $producto, array $datos, ?UploadedFile $imagen = null): Product
    {
        return DB::transaction(function () use ($producto, $datos, $imagen) {
            $anterior = $producto->image_path;

            if ($imagen) {
                $datos['image_path'] = $this->imagenes->guardar($imagen);
            }

            $producto->update($datos);

            if ($imagen && $anterior) {
                $this->imagenes->borrar($anterior);
            }

            return $producto->fresh();
        });
    }
}