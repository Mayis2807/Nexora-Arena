<?php

namespace Tests\Feature;

use App\Models\Asiento;
use App\Models\EstadoAsiento;
use App\Models\Evento;
use App\Models\Precio;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompraTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_usuario_puede_confirmar_compra(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $data = $this->crearReserva($user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->postJson('/api/compra', [
                             'reservas' => [$data['reserva']->id],
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('entradas', [
            'user_id' => $user->id,
            'evento_id' => $data['evento']->id,
            'asiento_id' => $data['asiento']->id,
        ]);
    }

    public function test_no_se_puede_comprar_reserva_expirada(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $data = $this->crearReserva($user);

        // Expirar la reserva
        $data['reserva']->update(['reservado_hasta' => now()->subHour()]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->postJson('/api/compra', [
                             'reservas' => [$data['reserva']->id],
                         ]);

        $response->assertStatus(400);
    }

    public function test_no_se_puede_comprar_reserva_ajena(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $token2 = $user2->createToken('test')->plainTextToken;
        $data = $this->crearReserva($user);

        $response = $this->withHeader('Authorization', "Bearer {$token2}")
                         ->postJson('/api/compra', [
                             'reservas' => [$data['reserva']->id],
                         ]);

        $response->assertStatus(400);
    }

    public function test_usuario_no_autenticado_no_puede_comprar(): void
    {
        $response = $this->postJson('/api/compra', [
            'reservas' => [1],
        ]);

        $response->assertStatus(401);
    }
}