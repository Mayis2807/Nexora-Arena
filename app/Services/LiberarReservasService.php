<?php

namespace App\Services;

use App\Models\EstadoAsiento;
use Illuminate\Support\Facades\Log;

class LiberarReservasService
{
    public function liberarExpiradas(): int
    {
        // Log antes de borrar (si lo necesitas)
        $expiradas = EstadoAsiento::expirados()->get();
        foreach ($expiradas as $reserva) {
            Log::info('Reserva expirada liberada', [
                'reserva_id' => $reserva->id,
                'evento_id'  => $reserva->evento_id,
                'asiento_id' => $reserva->asiento_id,
            ]);
        }

        // Borrado en una sola query
        return EstadoAsiento::expirados()->delete();
    }

    public function liberarDeUsuario(int $userId): int
    {
        return EstadoAsiento::expirados()
            ->where('user_id', $userId)
            ->delete();
    }
}