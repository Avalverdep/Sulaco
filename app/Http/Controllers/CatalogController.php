<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductImage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogController extends Controller
{
    public function index(Request $request){
        $categorias = Category::query()
            ->whereNull('parent_id')
            ->with('hijas:id,parent_id,name,slug')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $slug = $request->string('categoria')->toString();
        $seleccionada = $slug ? Category::where('slug', $slug)->first() : null;

        $productos = Product::query()
            ->whereIn('status', ['disponible', 'pedido'])
            ->with('categoria:id,name,slug')
            ->when($seleccionada, function ($q) use ($seleccionada) {
                $ids = $seleccionada->hijas()->pluck('id')->push($seleccionada->id);
                $q->whereIn('category_id', $ids);
            })
            ->when($request->filled('buscar'), fn ($q) =>
                $q->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($request->string('buscar')).'%'])
            )
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'nombre' => $p->name,
                'categoria' => $p->categoria->name,
                'precio' => number_format($p->price, 2, ',', '.').' €',
                'imagen' => ProductImage::url($p->image_path, true),
                'reservable' => $p->stock > 0,
                'proximamente' => $p->status === 'pedido',
            ]);

        return Inertia::render('Catalogo', [
            'productos' => $productos,
            'categorias' => $categorias,
            'filtros' => [
                'categoria' => $slug ?: null,
                'buscar' => $request->string('buscar')->toString() ?: null,
            ],
        ]);
    }
}