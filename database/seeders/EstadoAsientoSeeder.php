<?php

namespace Database\Seeders;

use App\Models\Asiento;
use App\Models\Evento;
use App\Models\Sector;
use App\Models\EstadoAsiento;
use Illuminate\Database\Seeder;

class EstadoAsientoSeeder extends Seeder
{
    public function run(): void
    {
        $evento = Evento::first();
        if (!$evento) return;

        $sectores = Sector::all();
        $total = $sectores->count();
        $mitad = (int) ceil($total / 4);
        $now = now();
        $batch = [];

        // Cargar todos los asientos de una sola query con su sector_id
        $asientosPorSector = Asiento::all()->groupBy('sector_id');

        foreach ($sectores as $index => $sector) {
            if ($index >= $mitad) continue;

            $porcentaje = match($index % 4) {
                0 => 100,
                1 => 85,
                2 => 15,
                3 => 50,
            };

            $asientos = $asientosPorSector->get($sector->id, collect());
            if ($asientos->isEmpty()) continue;

            $cantidad = (int) ceil($asientos->count() * $porcentaje / 100);

            foreach ($asientos->take($cantidad) as $asiento) {
                $batch[] = [
                    'evento_id'      => $evento->id,
                    'asiento_id'     => $asiento->id,
                    'estado'         => 'vendido',
                    'user_id'        => null,
                    'reservado_hasta'=> null,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];

                if (count($batch) >= 1000) {
                    EstadoAsiento::insert($batch);
                    $batch = [];
                }
            }

            $this->command->info("  → {$sector->nombre}: {$cantidad}/{$asientos->count()} asientos ocupados ({$porcentaje}%)");
        }

        if (!empty($batch)) {
            EstadoAsiento::insert($batch);
        }
    }
}