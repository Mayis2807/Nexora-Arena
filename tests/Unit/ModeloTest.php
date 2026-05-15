<?php

namespace Tests\Unit;

use App\Models\Asiento;
use App\Models\Entrada;
use App\Models\EstadoAsiento;
use App\Models\Evento;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModeloTest extends TestCase
{
    use RefreshDatabase;

    public function test_sector_tiene_asientos(): void
    {
        $sector = Sector::factory()->create();
        Asiento::factory()->count(5)->create(['sector_id' => $sector->id]);

        $this->assertEquals(5, $sector->totalAsientos());
    }

    public function test_sector_scope_activos(): void
    {
        Sector::factory()->count(3)->create(['activo' => true]);
        Sector::factory()->count(2)->create(['activo' => false]);

        $this->assertEquals(3, Sector::activos()->count());
    }

    public function test_asiento_disponible_para_evento(): void
    {
        $sector = Sector::factory()->create();
        $evento = Evento::factory()->create();
        $asiento = Asiento::factory()->create(['sector_id' => $sector->id]);

        $this->assertTrue($asiento->estaDisponibleParaEvento($evento->id));
    }

    public function test_asiento_no_disponible_si_esta_ocupado(): void
    {
        $sector = Sector::factory()->create();
        $evento = Evento::factory()->create();
        $asiento = Asiento::factory()->create(['sector_id' => $sector->id]);
        $user = User::factory()->create();

        EstadoAsiento::create([
            'evento_id' => $evento->id,
            'asiento_id' => $asiento->id,
            'user_id' => $user->id,
            'estado' => 'vendido',
        ]);

        $this->assertFalse($asiento->estaDisponibleParaEvento($evento->id));
    }

    public function test_evento_scope_futuros(): void
    {
        Evento::factory()->create(['fecha' => now()->addDays(10)->format('Y-m-d')]);
        Evento::factory()->create(['fecha' => now()->subDays(10)->format('Y-m-d')]);

        $this->assertEquals(1, Evento::futuros()->count());
    }

    public function test_evento_ya_paso(): void
    {
        $evento = Evento::factory()->create(['fecha' => now()->subDays(5)->format('Y-m-d')]);
        $this->assertTrue($evento->yaPaso());
    }

    public function test_estado_asiento_ha_expirado(): void
    {
        $sector = Sector::factory()->create();
        $evento = Evento::factory()->create();
        $asiento = Asiento::factory()->create(['sector_id' => $sector->id]);
        $user = User::factory()->create();

        $estado = EstadoAsiento::create([
            'evento_id' => $evento->id,
            'asiento_id' => $asiento->id,
            'user_id' => $user->id,
            'estado' => 'bloqueado',
            'reservado_hasta' => now()->subHour(),
        ]);

        $this->assertTrue($estado->haExpirado());
    }

    public function test_estado_asiento_no_expira_si_vendido(): void
    {
        $sector = Sector::factory()->create();
        $evento = Evento::factory()->create();
        $asiento = Asiento::factory()->create(['sector_id' => $sector->id]);
        $user = User::factory()->create();

        $estado = EstadoAsiento::create([
            'evento_id' => $evento->id,
            'asiento_id' => $asiento->id,
            'user_id' => $user->id,
            'estado' => 'vendido',
            'reservado_hasta' => now()->subHour(),
        ]);

        $this->assertFalse($estado->haExpirado());
    }

    public function test_entrada_genera_codigo_qr_automaticamente(): void
    {
        $sector = Sector::factory()->create();
        $evento = Evento::factory()->create();
        $asiento = Asiento::factory()->create(['sector_id' => $sector->id]);
        $user = User::factory()->create();

        $entrada = Entrada::create([
            'user_id' => $user->id,
            'evento_id' => $evento->id,
            'asiento_id' => $asiento->id,
            'precio_pagado' => 50.00,
        ]);

        $this->assertNotNull($entrada->codigo_qr);
        $this->assertStringStartsWith('QR-', $entrada->codigo_qr);
    }

    public function test_user_tiene_entradas(): void
    {
        $user = User::factory()->create();
        $sector = Sector::factory()->create();
        $evento = Evento::factory()->create();
        $asiento = Asiento::factory()->create(['sector_id' => $sector->id]);

        Entrada::create([
            'user_id' => $user->id,
            'evento_id' => $evento->id,
            'asiento_id' => $asiento->id,
            'precio_pagado' => 50.00,
        ]);

        $this->assertEquals(1, $user->entradas()->count());
    }
}