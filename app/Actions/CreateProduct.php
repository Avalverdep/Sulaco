<?php

namespace App\Actions;

use App\Models\Product;
use App\Services\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CreateProduct
{
    public function __construct(private ProductImage $imagenes) {}

    public function ejecutar(array $datos, ?UploadedFile $imagen = null): Product
    {
        return DB::transaction(function () use ($datos, $imagen) {
            $producto = Product::create($datos);

            if ($imagen) {
                $producto->update(['image_path' => $this->imagenes->guardar($imagen)]);
            }

            return $producto;
        });
    }
}