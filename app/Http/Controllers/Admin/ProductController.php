<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CreateProduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Actions\UpdateProduct;
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
                'categoria_id' => $p->category_id,
            ]);

        return Inertia::render('Admin/Productos/Index', ['productos' => $productos,'categorias' => Category::with('padre:id,name')
        ->orderBy('name')
        ->get(['id', 'name', 'parent_id'])
        ->map(fn ($c) => [
        'id' => $c->id,
        'name' => $c->padre ? "{$c->padre->name} · {$c->name}" : $c->name,
        ])
        ->sortBy('name')
        ->values(),]);
    }

    public function create(){
        return Inertia::render('Admin/Productos/Create', [
            'categorias' => Category::query()
                ->whereNull('parent_id')
                ->with('hijas:id,parent_id,name')
                ->orderBy('name')
                ->get(['id', 'name']),
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

    public function edit(Product $producto){
        return Inertia::render('Admin/Productos/Edit', [
            'producto' => [
                'id' => $producto->id,
                'name' => $producto->name,
                'category_id' => $producto->category_id,
                'description' => $producto->description,
                'price' => $producto->price,
                'status' => $producto->status,
                'max_per_user' => $producto->max_per_user,
                'reservation_days' => $producto->reservation_days,
                'stock' => $producto->stock,
                'imagen' => ProductImage::url($producto->image_path),
            ],
            'categorias' => Category::query()
                ->whereNull('parent_id')
                ->with('hijas:id,parent_id,name')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $producto, UpdateProduct $actualizar)
    {
        $actualizar->ejecutar(
            $producto,
            $request->safe()->except('imagen'),
            $request->file('imagen')
        );

        return redirect()
            ->route('admin.productos.index')
            ->with('exito', 'Producto actualizado.');
    }

    public function destroy(Product $producto, ProductImage $imagenes)
    {
        if ($producto->reservas()->exists()) {
            return back()->withErrors([
                'producto' => 'Este producto tiene reservas. Márcalo como descatalogado en vez de eliminarlo.',
            ]);
        }

        $imagenes->borrar($producto->image_path);
        $producto->delete();

        return redirect()->route('admin.productos.index')->with('exito', 'Producto eliminado.');
    }
}