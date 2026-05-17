<?php

namespace Database\Seeders;

use App\Models\Sector;
use App\Models\Asiento;
use Illuminate\Database\Seeder;

class AsientoSeeder extends Seeder
{
    public function run(): void
    {
        $sectores = Sector::all();
        $now = now();
        $batch = [];
        $totalAsientos = 0;

        foreach ($sectores as $sector) {
            $asientosDelSector = $this->generarAsientosPorSector($sector, $now);
            
            foreach ($asientosDelSector as $asiento) {
                $batch[] = $asiento;
                
                if (count($batch) >= 1000) {
                    Asiento::insert($batch);
                    $totalAsientos += count($batch);
                    $batch = [];
                }
            }
        }

        // Insertar los restantes
        if (!empty($batch)) {
            Asiento::insert($batch);
            $totalAsientos += count($batch);
        }

        $this->command->info("✅ Asientos creados: {$totalAsientos}");
    }

    private function generarAsientosPorSector(Sector $sector, $now): array
    {
        $asientos = [];

        if (preg_match('/^Sector (10[1-9]|1[1-2][0-9]|30[1-9]|3[1-2][0-9])$/', $sector->nombre)) {
            for ($fila = 1; $fila <= 20; $fila++) {
                for ($numero = 1; $numero <= 15; $numero++) {
                    $asientos[] = [
                        'sector_id' => $sector->id,
                        'fila' => (string) $fila,
                        'numero' => $numero,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        } elseif (str_starts_with($sector->nombre, 'Palco')) {
            for ($numero = 1; $numero <= 8; $numero++) {
                $asientos[] = [
                    'sector_id' => $sector->id,
                    'fila' => 'A',
                    'numero' => $numero,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        } elseif ($sector->nombre === 'CLUB') {
            for ($fila = 1; $fila <= 10; $fila++) {
                for ($numero = 1; $numero <= 20; $numero++) {
                    $asientos[] = [
                        'sector_id' => $sector->id,
                        'fila' => (string) $fila,
                        'numero' => $numero,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        } elseif ($sector->nombre === 'JOHNNIE WALKER') {
            for ($fila = 1; $fila <= 8; $fila++) {
                for ($numero = 1; $numero <= 15; $numero++) {
                    $asientos[] = [
                        'sector_id' => $sector->id,
                        'fila' => (string) $fila,
                        'numero' => $numero,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        } elseif ($sector->nombre === 'PISTA') {
            for ($fila = 1; $fila <= 30; $fila++) {
                for ($numero = 1; $numero <= 25; $numero++) {
                    $asientos[] = [
                        'sector_id' => $sector->id,
                        'fila' => (string) $fila,
                        'numero' => $numero,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        } elseif ($sector->nombre === 'FRONT STAGE') {
            for ($fila = 1; $fila <= 5; $fila++) {
                for ($numero = 1; $numero <= 30; $numero++) {
                    $asientos[] = [
                        'sector_id' => $sector->id,
                        'fila' => (string) $fila,
                        'numero' => $numero,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        return $asientos;
    }
}