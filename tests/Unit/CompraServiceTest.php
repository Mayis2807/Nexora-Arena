<?php

namespace Tests\Unit;

use App\Models\Asiento;
use App\Models\EstadoAsiento;
use App\Models\Evento;
use App\Models\Precio;
use App\Models\Sector;
use App\Models\User;
use App\Services\CompraService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompraServiceTest extends TestCase
{
    use RefreshDatabase;

    private CompraService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CompraService();
    }

    private function crearReserva(User $user): array
    {
        $sector = Sector::factory()->create();
        $evento = Evento::factory()->create();
        $asiento = Asiento::factory()->create(['sector_id' => $sector->id]);
        Precio::factory()->create([
            'evento_id' => $evento->id,
            'sector_id' => $sector->id,
            'precio' => 50.00,
            'disponible' => true,
        ]);

        $reserva = EstadoAsiento::create([
            'evento_id' => $evento->id,
            'asiento_id' => $asiento->id,
            'user_id' => $user->id,
            'estado' => 'bloqueado',
            'reservado_hasta' => now()->addDay(),
        ]);

        return compact('sector', 'evento', 'asiento', 'reserva');
    }

    public function test_puede_procesar_compra(): void
    {
        $user = User::factory()->create();
        $data = $this->crearReserva($user);

        $entradas = $this->service->procesarCompra(
            [$data['reserva']->id],
            $user->id
        );

        $this->assertEquals(1, $entradas->count());
        $this->assertDatabaseHas('entradas', [
            'user_id' => $user->id,
            'evento_id' => $data['evento']->id,
        ]);
    }

    public function test_no_puede_comprar_reserva_expirada(): void
    {
        $user = User::factory()->create();
        $data = $this->crearReserva($user);

        $data['reserva']->update(['reservado_hasta' => now()->subHour()]);

        $this->expectException(\Exception::class);

        $this->service->procesarCompra([$data['reserva']->id], $user->id);
    }

    public function test_reserva_se_marca_como_vendida(): void
    {
        $user = User::factory()->create();
        $data = $this->crearReserva($user);

        $this->service->procesarCompra([$data['reserva']->id], $user->id);

        $this->assertDatabaseHas('estado_asientos', [
            'id' => $data['reserva']->id,
            'estado' => 'vendido',
        ]);
    }

    public function test_entrada_tiene_precio_correcto(): void
    {
        $user = User::factory()->create();
        $data = $this->crearReserva($user);

        $entradas = $this->service->procesarCompra([$data['reserva']->id], $user->id);

        $this->assertEquals(50.00, $entradas->first()->precio_pagado);
    }
}