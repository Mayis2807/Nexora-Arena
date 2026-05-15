<?php

namespace Database\Factories;

use App\Models\Evento;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventoFactory extends Factory
{
    protected $model = Evento::class;

    public function definition(): array
    {
        return [
            'nombre'            => $this->faker->sentence(3),
            'descripcion_corta' => $this->faker->sentence(10),
            'descripcion_larga' => $this->faker->paragraph(),
            'fecha'             => $this->faker->unique()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'hora'              => $this->faker->time('H:i'),
            'poster_url'        => $this->faker->imageUrl(640, 480, 'events', true),
        ];
    }
}