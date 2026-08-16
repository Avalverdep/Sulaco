<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = DB::table('users')->insertGetId([
            'google_id' => null,
            'name' => 'adminuser',
            'email' => 'admin@admin.com',
            'role' => 'admin',
            'password' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tipos = DB::table('event_types')->pluck('id', 'slug');

        $eventos = [
            ['tipo' => 'wargame', 'title' => 'Torneo Warhammer 40k', 'dias' => 3, 'hora' => '10:00', 'dur' => 4, 'capacity' => 12],
            ['tipo' => 'tcg', 'title' => 'Liga Pokémon TCG', 'dias' => 5, 'hora' => '17:00', 'dur' => 3, 'capacity' => 24],
            ['tipo' => 'rol', 'title' => 'Partida de iniciación al rol', 'dias' => 8, 'hora' => '17:30', 'dur' => 3, 'capacity' => 5],
            ['tipo' => 'tcg', 'title' => 'Torneo Magic Commander', 'dias' => 10, 'hora' => '16:30', 'dur' => 4, 'capacity' => 16],
            ['tipo' => 'juego-de-mesa', 'title' => 'Noche de juegos de mesa', 'dias' => 12, 'hora' => '18:00', 'dur' => 4, 'capacity' => 20],
        ];

        foreach ($eventos as $e) {
            $inicio = now()->addDays($e['dias'])->setTimeFromTimeString($e['hora']);

            DB::table('events')->insert([
                'kind' => 'evento_tienda',
                'event_type_id' => $tipos[$e['tipo']],
                'created_by' => $adminId,
                'title' => $e['title'],
                'starts_at' => $inicio,
                'ends_at' => $inicio->copy()->addHours($e['dur']),
                'capacity' => $e['capacity'],
                'status' => 'aprobado',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}