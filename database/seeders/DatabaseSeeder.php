<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SectorSeeder::class,
            AsientoSeeder::class,
            UserSeeder::class,
            EventoSeeder::class,
            PrecioSeeder::class,
            EstadoAsientoSeeder::class,
        ]);
    }
}