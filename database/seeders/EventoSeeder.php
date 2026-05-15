<?php

namespace Database\Seeders;

use App\Models\Evento;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        $eventos = [
            [
                'nombre' => 'Concierto Aventura 2026',
                'descripcion_corta' => 'El mejor concierto de Aventura del año',
                'descripcion_larga' => 'Disfruta de una noche inolvidable con lo mejor de la bachata. Un espectáculo único que no te puedes perder.',
                'poster_url' => 'imagenes/aventura.png',
                'fecha' => '2026-06-15',
                'hora' => '20:00',
            ],
            [
                'nombre' => 'Final de Baloncesto',
                'descripcion_corta' => 'Gran final de temporada en el Baloncesto',
                'descripcion_larga' => 'Vive la emoción de la final de la en directo. Los dos mejores equipos se enfrentan por el título.',
                'poster_url' => 'imagenes/baloncesto.png',
                'fecha' => '2026-07-20',
                'hora' => '21:00',
            ],
            [
                'nombre' => 'NEXORA Tech Conference',
                'descripcion_corta' => 'Innovación, IA y el futuro de la tecnología reunidos en un solo lugar.',
                'descripcion_larga' => 'Un congreso diseñado para conectar startups, desarrolladores, inversores y 
                                        empresas líderes del sector tecnológico a través de conferencias, demostraciones en vivo,  
                                        networking y experiencias inmersivas impulsadas por inteligencia artificial.',
                'poster_url' => 'imagenes/tech.jpeg',
                'fecha' => '2026-05-19',
                'hora' => '18:30',
            ],
            [
                'nombre' => 'Festival Electrónica',
                'descripcion_corta' => 'Los mejores DJs del mundo',
                'descripcion_larga' => 'Festival de música electrónica con los DJs más reconocidos a nivel mundial. Una experiencia única de sonido y luces.',
                'poster_url' => 'imagenes/electro.png',
                'fecha' => '2026-08-10',
                'hora' => '19:00',
            ],
            [
                'nombre' => 'Obra de Teatro Clásico',
                'descripcion_corta' => 'Teatro clásico español',
                'descripcion_larga' => 'Representación de una obra clásica del teatro español con los mejores actores del país.',
                'poster_url' => 'imagenes/leon.jpeg',
                'fecha' => '2026-09-05',
                'hora' => '18:30',
            ],
        ];

        foreach ($eventos as $evento) {
            Evento::create($evento);
        }

        $this->command->info('✅ Eventos creados: ' . count($eventos));
    }
}