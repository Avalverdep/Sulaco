<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void{
        $principales = [
            'juegos-de-mesa' => 'Juegos de mesa',
            'wargames' => 'Wargames',
            'tcg' => 'Cartas y TCG',
            'rol' => 'Rol',
            'modelismo' => 'Modelismo y pinturas',
            'accesorios' => 'Accesorios',
        ];

        foreach ($principales as $slug => $nombre) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $slug],
                ['name' => $nombre, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // PROVISIONAL: pendiente de confirmar con el cliente.
        $subcategorias = [
            'tcg' => [
                'magic' => 'Magic',
                'yugioh' => 'Yu-Gi-Oh!',
                'one-piece' => 'One Piece',
                'digimon' => 'Digimon',
                'pokemon' => 'Pokémon',
            ],
            'wargames' => [
                'warhammer-40k' => 'Warhammer 40.000',
                'age-of-sigmar' => 'Age of Sigmar',
            ],
        ];

        foreach ($subcategorias as $slugPadre => $hijas) {
            $padreId = DB::table('categories')->where('slug', $slugPadre)->value('id');

            foreach ($hijas as $slug => $nombre) {
                DB::table('categories')->updateOrInsert(
                    ['slug' => $slug],
                    ['name' => $nombre, 'parent_id' => $padreId, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}