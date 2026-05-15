<?php

namespace Tests\Feature;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventoTest extends TestCase
{
    use RefreshDatabase;

    public function test_cualquiera_puede_ver_eventos(): void
    {
        Evento::factory()->create(['fecha' => '2027-01-01']);
        Evento::factory()->create(['fecha' => '2027-02-01']);
        Evento::factory()->create(['fecha' => '2027-03-01']);
    
        $response = $this->getJson('/api/eventos');
    
        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    public function test_cualquiera_puede_ver_detalle_evento(): void
    {
        $evento = Evento::factory()->create();

        $response = $this->getJson("/api/eventos/{$evento->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $evento->id]);
    }

    public function test_admin_puede_crear_evento(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $token = $admin->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->postJson('/api/admin/eventos', [
                             'nombre' => 'Nuevo Evento',
                             'descripcion_corta' => 'Descripción corta',
                             'descripcion_larga' => 'Descripción larga del evento',
                             'fecha' => '2027-01-15',
                             'hora' => '20:00',
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('eventos', ['nombre' => 'Nuevo Evento']);
    }

    public function test_usuario_normal_no_puede_crear_evento(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->postJson('/api/admin/eventos', [
                             'nombre' => 'Evento No Autorizado',
                             'descripcion_corta' => 'Descripción',
                             'descripcion_larga' => 'Descripción larga',
                             'fecha' => '2027-02-15',
                             'hora' => '20:00',
                         ]);

        $response->assertStatus(403);
    }

    public function test_admin_puede_eliminar_evento_sin_entradas(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $token = $admin->createToken('test')->plainTextToken;
        $evento = Evento::factory()->create();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                         ->deleteJson("/api/admin/eventos/{$evento->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('eventos', ['id' => $evento->id]);
    }

    public function test_evento_no_encontrado_retorna_404(): void
    {
        $response = $this->getJson('/api/eventos/99999');
        $response->assertStatus(404);
    }
}