<?php

namespace Database\Factories;

use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectorFactory extends Factory
{
    protected $model = Sector::class;

    public function definition(): array
    {
        $tipo = $this->faker->randomElement(['lateral', 'superior', 'palco']);

        $nombre = match($tipo) {
            'lateral' => 'Sector ' . $this->faker->numberBetween(101, 122),
            'superior' => 'Sector ' . $this->faker->numberBetween(301, 323),
            'palco'   => 'Palco ' . $this->faker->numberBetween(1, 22),
        };

        $descripcion = match($tipo) {
            'lateral' => 'Grada lateral',
            'superior' => 'Grada superior',
            'palco'   => 'Palco VIP',
        };

        return [
            'nombre'      => $nombre,
            'descripcion' => $descripcion,
            'activo'      => true,
        ];
    }
}