<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    $this->call([
    EventTypeSeeder::class,
    CategorySeeder::class,
    EventSeeder::class,
    ]);

    /**
     * Seed the application's database.
     */
    public function run(): void{
        $this->call([
            EventTypeSeeder::class,
            EventSeeder::class,
        ]);
    }
}
