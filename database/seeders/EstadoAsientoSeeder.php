<?php

namespace Database\Seeders;

use App\Models\Asiento;
use App\Models\Evento;
use App\Models\EstadoAsiento;
use Illuminate\Database\Seeder;

class EstadoAsientoSeeder extends Seeder
{
    public function run(): void
    {
        $evento = Evento::first();
        if (!$evento) return;

        $sectores = \App\Models\Sector::all();
        $total = $sectores->count();
        $mitad = (int) ceil($total / 2);

        foreach ($sectores as $index => $sector) {
            if ($index < $mitad) {
                // Primera mitad — variedad de ocupación
                $porcentaje = match($index % 4) {
                    0 => 100, // Lleno
                    1 => 85,  // Casi lleno
                    2 => 15,  // Pocas entradas
                    3 => 50,  // Mitad
                };
                $this->llenarSector($evento->id, $sector->nombre, $porcentaje);
            }
            // Segunda mitad — libres (no hacemos nada)
        }

    }

    private function llenarSector($eventoId, $nombreSector, $porcentaje)
    {
        $asientos = Asiento::whereHas('sector', fn($q) => $q->where('nombre', $nombreSector))
            ->get();

        if ($asientos->isEmpty()) return;

        $cantidad = (int) ceil($asientos->count() * $porcentaje / 100);
        $asientosALlenar = $asientos->take($cantidad);

        foreach ($asientosALlenar as $asiento) {
            EstadoAsiento::firstOrCreate(
                ['evento_id' => $eventoId, 'asiento_id' => $asiento->id],
                ['estado' => 'vendido', 'user_id' => null]
            );
        }

        $this->command->info("  → {$nombreSector}: {$cantidad}/{$asientos->count()} asientos ocupados ({$porcentaje}%)");
    }
}