<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $sectores = [];

        for ($i = 101; $i <= 122; $i++) {
            $sectores[] = ['nombre' => "Sector $i", 'descripcion' => 'Grada lateral', 'activo' => true, 'created_at' => $now, 'updated_at' => $now];
        }

        for ($i = 301; $i <= 323; $i++) {
            $sectores[] = ['nombre' => "Sector $i", 'descripcion' => 'Grada superior', 'activo' => true, 'created_at' => $now, 'updated_at' => $now];
        }

        for ($i = 1; $i <= 22; $i++) {
            $sectores[] = ['nombre' => "Palco $i", 'descripcion' => 'Palco VIP', 'activo' => true, 'created_at' => $now, 'updated_at' => $now];
        }

        $sectores[] = ['nombre' => 'CLUB', 'descripcion' => 'Zona Club', 'activo' => true, 'created_at' => $now, 'updated_at' => $now];
        $sectores[] = ['nombre' => 'JOHNNIE WALKER', 'descripcion' => 'Zona Johnnie Walker', 'activo' => true, 'created_at' => $now, 'updated_at' => $now];
        $sectores[] = ['nombre' => 'PISTA', 'descripcion' => 'Pista central', 'activo' => true, 'created_at' => $now, 'updated_at' => $now];
        $sectores[] = ['nombre' => 'FRONT STAGE', 'descripcion' => 'Frente al escenario', 'activo' => true, 'created_at' => $now, 'updated_at' => $now];

        Sector::insert($sectores);

        $this->command->info('✅ Sectores creados: ' . count($sectores));
    }
}