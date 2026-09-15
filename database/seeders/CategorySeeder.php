<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['name' => 'Juegos de mesa', 'slug' => 'juegos-de-mesa'],
            ['name' => 'Wargames', 'slug' => 'wargames'],
            ['name' => 'Cartas y TCG', 'slug' => 'tcg'],
            ['name' => 'Rol', 'slug' => 'rol'],
            ['name' => 'Modelismo y pinturas', 'slug' => 'modelismo'],
            ['name' => 'Accesorios', 'slug' => 'accesorios'],
        ];

        foreach ($categorias as $categoria) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $categoria['slug']],
                ['name' => $categoria['name'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}