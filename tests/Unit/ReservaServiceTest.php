<?php

namespace Tests\Unit;

use App\Models\Asiento;
use App\Models\Evento;
use App\Models\Precio;
use App\Models\Sector;
use App\Models\User;
use App\Services\ReservaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReservaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReservaService();
    }

    private function crearEscenario(): array
    {
        $sector = Sector::factory()->create();
        $evento = Evento::factory()->create();
        $asiento = Asiento::factory()->create(['sector_id' => $sector->id]);
        Precio::factory()->create([
            'evento_id' => $evento->id,
            'sector_id' => $sector->id,
            'disponible' => true,
        ]);
        $user = User::factory()->create();

        return compact('sector', 'evento', 'asiento', 'user');
    }

    public function test_puede_reservar_asiento(): void
    {
        $data = $this->crearEscenario();

        $reserva = $this->service->reservarAsiento(
            $data['evento']->id,
            $data['asiento']->id,
            $data['user']->id
        );

        $this->assertEquals('bloqueado', $reserva->estado);
        $this->assertEquals($data['user']->id, $reserva->user_id);
    }

    public function test_no_puede_reservar_asiento_ocupado(): void
    {
        $data = $this->crearEscenario();
        $user2 = User::factory()->create();

        $this->service->reservarAsiento(
            $data['evento']->id,
            $data['asiento']->id,
            $data['user']->id
        );

        $this->expectException(\Exception::class);

        $this->service->reservarAsiento(
            $data['evento']->id,
            $data['asiento']->id,
            $user2->id
        );
    }

    public function test_puede_cancelar_reserva(): void
    {
        $data = $this->crearEscenario();

        $reserva = $this->service->reservarAsiento(
            $data['evento']->id,
            $data['asiento']->id,
            $data['user']->id
        );

        $resultado = $this->service->cancelarReserva($reserva->id, $data['user']->id);

        $this->assertTrue($resultado);
        $this->assertDatabaseMissing('estado_asientos', ['id' => $reserva->id]);
    }

    public function test_obtener_reservas_activas(): void
    {
        $data = $this->crearEscenario();

        $this->service->reservarAsiento(
            $data['evento']->id,
            $data['asiento']->id,
            $data['user']->id
        );

        $reservas = $this->service->obtenerReservasActivas($data['user']->id);

        $this->assertEquals(1, $reservas->count());
    }

    public function test_no_puede_reservar_sector_no_disponible(): void
    {
        $sector = Sector::factory()->create();
        $evento = Evento::factory()->create();
        $asiento = Asiento::factory()->create(['sector_id' => $sector->id]);
        Precio::factory()->create([
            'evento_id' => $evento->id,
            'sector_id' => $sector->id,
            'disponible' => false,
        ]);
        $user = User::factory()->create();

        $this->expectException(\Exception::class);

        $this->service->reservarAsiento($evento->id, $asiento->id, $user->id);
    }
}