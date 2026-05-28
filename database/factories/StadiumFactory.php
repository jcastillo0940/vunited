<?php

namespace Database\Factories;

use App\Domain\Stadium\Models\Stadium;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Stadium> */
class StadiumFactory extends Factory
{
    protected $model = Stadium::class;

    public function definition(): array
    {
        return [
            'name'            => 'Estadio Atalaya',
            'subtitle'        => 'Casa oficial de Veraguas United FC',
            'location'        => 'Santiago, Veraguas, Panamá',
            'address'         => 'Vía Atalaya, Santiago de Veraguas',
            'capacity'        => '8,500',
            'venue_type'      => 'Sede principal del club',
            'hero_image_path' => null,
            'map_embed_url'   => null,
            'zones'           => [
                ['id' => 'general', 'name' => 'General', 'badge' => 'SUR / NORTE', 'description' => 'Acceso amplio para vivir la grada popular.', 'feature' => 'Ambiente de barra y visión abierta del campo.'],
                ['id' => 'preferencial', 'name' => 'Preferencial', 'badge' => 'ESTE / OESTE', 'description' => 'Mejor cercanía al juego y acceso ágil.', 'feature' => 'Asientos más cómodos y mejor ángulo.'],
            ],
            'matchday'        => [
                ['id' => 1, 'icon' => 'route', 'title' => 'Llegada al estadio', 'description' => 'Accesos perimetrales claros e ingreso escalonado.'],
                ['id' => 2, 'icon' => 'event_seat', 'title' => 'Accesos y gradas', 'description' => 'Orientación por zonas y puertas señalizadas.'],
            ],
            'rules'           => [
                'Llega con anticipación para evitar filas en accesos principales.',
                'Ten tu boleto listo antes de ingresar a la zona.',
                'Respeta las indicaciones del personal operativo.',
                'Evita objetos prohibidos y sigue la señalización del recinto.',
            ],
            'is_active'       => true,
            'metadata'        => [
                'hero_title'       => 'ESTADIO',
                'hero_highlight'   => 'ATALAYA',
                'hero_description' => 'La casa del rugido indio. Un punto de encuentro para la provincia, el fútbol y la energía de cada jornada.',
                'map_title'        => 'Ubicación del estadio',
                'map_description'  => 'Consulta la ubicación, accesos y recomendaciones de estacionamiento.',
                'map_pin_label'    => 'ATALAYA',
                'map_action_label' => 'ABRIR EN GOOGLE MAPS',
                'map_action_href'  => 'https://maps.google.com',
                'info_action_label' => 'CÓMO LLEGAR',
                'info_action_href'  => 'https://maps.google.com',
                'cta_title'        => 'VIVE EL PARTIDO DESDE LA CASA DEL INDIO',
                'cta_description'  => 'Consulta zonas y prepárate para tu próxima jornada junto al club.',
                'cta_action_label' => 'COMPRAR BOLETOS',
                'cta_action_href'  => '/boletos',
            ],
        ];
    }
}
