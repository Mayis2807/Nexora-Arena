<?php

namespace Tests\Feature;

use App\Models\Asiento;
use App\Models\Evento;
use App\Models\Precio;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaTest extends TestCase
{
    use RefreshDatabase;

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

        return compact('sector', 'evento', 'asiento');
    }

    public function test_usuario_puede_reservar_asiento(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $data = $this->crearEscenario();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->postJson('/api/reservas', [
                             'evento_id' => $data['evento']->id,
                             'asiento_id' => $data['asiento']->id,
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('estado_asientos', [
            'evento_id' => $data['evento']->id,
            'asiento_id' => $data['asiento']->id,
            'estado' => 'bloqueado',
        ]);
    }

    public function test_no_se_puede_reservar_asiento_ocupado(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $data = $this->crearEscenario();

        // Primera reserva
        $this->withHeader('Authorization', "Bearer {$token}")
             ->postJson('/api/reservas', [
                 'evento_id' => $data['evento']->id,
                 'asiento_id' => $data['asiento']->id,
             ]);

        // Segunda reserva del mismo asiento
        $token2 = $user2->createToken('test')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer {$token2}")
                         ->postJson('/api/reservas', [
                             'evento_id' => $data['evento']->id,
                             'asiento_id' => $data['asiento']->id,
                         ]);

        $response->assertStatus(400);
    }

    public function test_usuario_no_autenticado_no_puede_reservar(): void
    {
        $data = $this->crearEscenario();

        $response = $this->postJson('/api/reservas', [
            'evento_id' => $data['evento']->id,
            'asiento_id' => $data['asiento']->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_usuario_puede_ver_sus_reservas(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->getJson('/api/reservas');

        $response->assertStatus(200);
    }

    public function test_usuario_puede_cancelar_su_reserva(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $data = $this->crearEscenario();

        // Crear reserva
        $reservaResponse = $this->withHeader('Authorization', "Bearer {$token}")
                                ->postJson('/api/reservas', [
                                    'evento_id' => $data['evento']->id,
                                    'asiento_id' => $data['asiento']->id,
                                ]);

        $reservaId = $reservaResponse->json('data.id');

        // Cancelar reserva
        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->deleteJson("/api/reservas/{$reservaId}");

        $response->assertStatus(200);
    }

    public function test_usuario_no_puede_cancelar_reserva_ajena(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $data = $this->crearEscenario();
    
        // Primera petición como user1
        $reservaResponse = $this->actingAs($user)
                                ->postJson('/api/reservas', [
                                    'evento_id' => $data['evento']->id,
                                    'asiento_id' => $data['asiento']->id,
                                ]);
    
        $reservaId = $reservaResponse->json('data.id');
    
        // Segunda petición como user2
        $response = $this->actingAs($user2)
                         ->deleteJson("/api/reservas/{$reservaId}");
    
        $response->assertStatus(400);
    }
}