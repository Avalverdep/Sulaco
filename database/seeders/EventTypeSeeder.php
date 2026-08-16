<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['name' => 'Wargame', 'slug' => 'wargame'],
            ['name' => 'Juego de cartas', 'slug' => 'tcg'],
            ['name' => 'Rol', 'slug' => 'rol'],
            ['name' => 'Juego de mesa', 'slug' => 'juego-de-mesa'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('event_types')->updateOrInsert(
                ['slug' => $tipo['slug']],
                ['name' => $tipo['name'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}