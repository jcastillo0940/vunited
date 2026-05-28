<?php

namespace Database\Seeders;

use App\Domain\Stadium\Models\Stadium;
use Illuminate\Database\Seeder;

class StadiumSeeder extends Seeder
{
    public function run(): void
    {
        Stadium::query()->updateOrCreate(
            ['name' => 'Estadio Atalaya'],
            [
                'subtitle'        => 'Casa oficial de Veraguas United FC',
                'location'        => 'Santiago, Veraguas, Panamá',
                'address'         => 'Vía Atalaya, Santiago de Veraguas',
                'capacity'        => '8,500',
                'venue_type'      => 'Sede principal del club',
                'hero_image_path' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&w=1600&q=80',
                'map_embed_url'   => null,
                'zones'           => [
                    ['id' => 'noreste',  'name' => 'Noreste',  'badge' => 'PORTERÍA NORTE', 'description' => 'Tribuna ubicada detrás de la portería norte. Máxima cercanía al arco y ambiente de barra.',          'feature' => 'Vista frontal al arco, alta energía e ingreso por acceso norte.'],
                    ['id' => 'suroeste', 'name' => 'Suroeste', 'badge' => 'PORTERÍA SUR',   'description' => 'Tribuna detrás de la portería sur, frente a la zona Noreste. Alta concentración de afición local.', 'feature' => 'Ambiente de barra, vista directa al arco sur.'],
                    ['id' => 'sureste',  'name' => 'Sureste',  'badge' => 'LATERAL',         'description' => 'Tribuna lateral a lo largo del campo. Visión completa del juego de extremo a extremo.',             'feature' => 'Mejor ángulo del partido, vista panorámica del terreno de juego.'],
                    ['id' => 'palcos',   'name' => 'Palcos',   'badge' => 'VIP · 10 PERS.',  'description' => 'Palcos privados con capacidad para 10 personas. Acceso exclusivo y hospitalidad premium.',          'feature' => 'Espacio privado, servicio preferencial e ingreso independiente.'],
                ],
                'matchday'        => [
                    ['id' => 1, 'icon' => 'route',         'title' => 'Llegada al estadio',       'description' => 'Accesos perimetrales claros, ingreso escalonado y apoyo logístico en jornada.'],
                    ['id' => 2, 'icon' => 'event_seat',    'title' => 'Accesos y gradas',          'description' => 'Orientación por zonas, puertas señalizadas y experiencia de tribuna ordenada.'],
                    ['id' => 3, 'icon' => 'local_dining',  'title' => 'Comida y activaciones',    'description' => 'Puntos de venta, fan zone y experiencia de comunidad antes y después del partido.'],
                    ['id' => 4, 'icon' => 'verified_user', 'title' => 'Seguridad y asistencia',   'description' => 'Protocolos de ingreso, personal de apoyo y recomendaciones para una visita segura.'],
                    ['id' => 5, 'icon' => 'shopping_bag',  'title' => 'Tienda y fan zone',        'description' => 'Espacios comerciales y de experiencia alrededor del colorido del club.'],
                ],
                'rules'           => [
                    'Llega con anticipación para evitar filas en accesos principales.',
                    'Ten tu boleto listo antes de ingresar a la zona.',
                    'Respeta las indicaciones del personal operativo y de seguridad.',
                    'Evita objetos prohibidos y sigue la señalización del recinto.',
                ],
                'is_active'       => true,
                'metadata'        => [
                    'hero_title'        => 'ESTADIO',
                    'hero_highlight'    => 'ATALAYA',
                    'hero_description'  => 'La casa del rugido indio. Un punto de encuentro para la provincia, el fútbol y la energía de cada jornada en Veraguas.',
                    'map_title'         => 'Ubicación del estadio',
                    'map_description'   => 'Consulta la ubicación, accesos oficiales y recomendaciones de estacionamiento.',
                    'map_pin_label'     => 'ATALAYA',
                    'map_action_label'  => 'ABRIR EN GOOGLE MAPS',
                    'map_action_href'   => 'https://maps.google.com',
                    'info_action_label' => 'CÓMO LLEGAR',
                    'info_action_href'  => 'https://maps.google.com',
                    'cta_title'         => 'VIVE EL PARTIDO DESDE LA CASA DEL INDIO',
                    'cta_description'   => 'Consulta zonas, conoce la experiencia de estadio y prepárate para tu próxima jornada junto al club.',
                    'cta_action_label'  => 'COMPRAR BOLETOS',
                    'cta_action_href'   => '/boletos',
                ],
            ],
        );
    }
}
