<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CreateProduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductImage;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $productos = Product::query()
            ->with('categoria')
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'nombre' => $p->name,
                'categoria' => $p->categoria->name,
                'precio' => number_format($p->price, 2, ',', '.').' €',
                'stock' => $p->stock,
                'estado' => $p->status,
                'miniatura' => ProductImage::url($p->image_path, true),
            ]);

        return Inertia::render('Admin/Productos/Index', ['productos' => $productos]);
    }

    public function create()
    {
        return Inertia::render('Admin/Productos/Create', [
            'categorias' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreProductRequest $request, CreateProduct $crear)
    {
        $crear->ejecutar(
            $request->safe()->except('imagen'),
            $request->file('imagen')
        );

        return redirect()
            ->route('admin.productos.index')
            ->with('exito', 'Producto añadido al inventario.');
    }
}