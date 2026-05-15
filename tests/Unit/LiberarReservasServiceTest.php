<?php

namespace Tests\Unit;

use App\Models\Asiento;
use App\Models\EstadoAsiento;
use App\Models\Evento;
use App\Models\Sector;
use App\Models\User;
use App\Services\LiberarReservasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiberarReservasServiceTest extends TestCase
{
    use RefreshDatabase;

    private LiberarReservasService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LiberarReservasService();
    }

    private function crearReserva(bool $expirada = false): EstadoAsiento
    {
        $sector = Sector::factory()->create();
        $evento = Evento::factory()->create();
        $asiento = Asiento::factory()->create(['sector_id' => $sector->id]);
        $user = User::factory()->create();

        return EstadoAsiento::create([
            'evento_id' => $evento->id,
            'asiento_id' => $asiento->id,
            'user_id' => $user->id,
            'estado' => 'bloqueado',
            'reservado_hasta' => $expirada ? now()->subHour() : now()->addDay(),
        ]);
    }

    public function test_libera_reservas_expiradas(): void
    {
        $this->crearReserva(expirada: true);
        $this->crearReserva(expirada: true);
        $this->crearReserva(expirada: false);

        $liberadas = $this->service->liberarExpiradas();

        $this->assertEquals(2, $liberadas);
        $this->assertEquals(1, EstadoAsiento::count());
    }

    public function test_no_libera_reservas_activas(): void
    {
        $this->crearReserva(expirada: false);
        $this->crearReserva(expirada: false);

        $liberadas = $this->service->liberarExpiradas();

        $this->assertEquals(0, $liberadas);
        $this->assertEquals(2, EstadoAsiento::count());
    }

    public function test_libera_reservas_de_usuario_especifico(): void
    {
        $sector = Sector::factory()->create();
        $evento = Evento::factory()->create();
        $user = User::factory()->create();

        $asiento1 = Asiento::factory()->create(['sector_id' => $sector->id]);
        $asiento2 = Asiento::factory()->create(['sector_id' => $sector->id]);

        EstadoAsiento::create([
            'evento_id' => $evento->id,
            'asiento_id' => $asiento1->id,
            'user_id' => $user->id,
            'estado' => 'bloqueado',
            'reservado_hasta' => now()->subHour(),
        ]);

        EstadoAsiento::create([
            'evento_id' => $evento->id,
            'asiento_id' => $asiento2->id,
            'user_id' => $user->id,
            'estado' => 'bloqueado',
            'reservado_hasta' => now()->subHour(),
        ]);

        $liberadas = $this->service->liberarDeUsuario($user->id);

        $this->assertEquals(2, $liberadas);
    }
}