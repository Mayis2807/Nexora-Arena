<?php

namespace App\Console\Commands;

// Importa
use App\Services\LiberarReservasService;
use Illuminate\Console\Command;

class LiberarReservasExpiradas extends Command
{
    protected $signature = 'reservas:liberar';
    protected $description = 'Libera las reservas expiradas';

    public function handle(LiberarReservasService $service)
    {
        // Llama al servicio para liberar las reservas expiradas
        // y guarda cuántas reservas fueron liberadas
        $count = $service->liberarExpiradas();
        $this->info("Se liberaron {$count} reservas expiradas.");
        return 0;
    }
}
