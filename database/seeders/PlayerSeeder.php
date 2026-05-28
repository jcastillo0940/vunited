<?php

namespace Database\Seeders;

use App\Domain\Squad\Models\Player;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $players = [
            // First team
            ['name' => 'Marcos Allen',    'number' => '01', 'position' => 'Portero',   'position_key' => 'goalkeeper', 'category' => 'first-team',  'nationality' => 'Panameño',   'height' => '1.89 m', 'dominant_foot' => 'Derecho', 'biography' => 'Arquero de grandes reflejos y liderazgo silencioso, Marcos organiza la última línea y transmite seguridad en cada salida.', 'sort_order' => 1],
            ['name' => 'Luis Torres',     'number' => '04', 'position' => 'Defensa',   'position_key' => 'defender',   'category' => 'first-team',  'nationality' => 'Panameño',   'height' => '1.80 m', 'dominant_foot' => 'Derecho', 'biography' => 'Defensa sólido y leal. Luis es la roca de la retaguardia india.', 'sort_order' => 2],
            ['name' => 'Javier Guerra',   'number' => '10', 'position' => 'Volante',   'position_key' => 'midfielder', 'category' => 'first-team',  'nationality' => 'Panameño',   'height' => '1.78 m', 'dominant_foot' => 'Derecho', 'biography' => 'Capitán del mediocampo indio. Javier marca el ritmo del juego y conecta la intensidad del club con claridad en cada posesión.', 'sort_order' => 3, 'stats' => [['key'=>'matches','label'=>'Partidos Jugados','value'=>'19','tone'=>'primary'],['key'=>'goals','label'=>'Goles Anotados','value'=>'06','tone'=>'accent'],['key'=>'assists','label'=>'Asistencias','value'=>'09','tone'=>'primary'],['key'=>'minutes','label'=>'Minutos','value'=>'1,691','tone'=>'neutral']], 'attributes' => [['key'=>'vision','label'=>'Visión','value'=>92],['key'=>'passing','label'=>'Pase','value'=>90],['key'=>'stamina','label'=>'Resistencia','value'=>86],['key'=>'dribbling','label'=>'Regate','value'=>84]]],
            ['name' => 'Alexis Canto',    'number' => '09', 'position' => 'Delantero', 'position_key' => 'forward',    'category' => 'first-team',  'nationality' => 'Panameño',   'height' => '1.84 m', 'dominant_foot' => 'Derecho', 'biography' => 'Delantero explosivo con olfato goleador y hambre competitiva. Alexis castiga espacios cortos y largos con la misma determinación.', 'sort_order' => 4, 'stats' => [['key'=>'matches','label'=>'Partidos Jugados','value'=>'18','tone'=>'primary'],['key'=>'goals','label'=>'Goles Anotados','value'=>'12','tone'=>'accent'],['key'=>'assists','label'=>'Asistencias','value'=>'05','tone'=>'primary'],['key'=>'minutes','label'=>'Minutos','value'=>'1,542','tone'=>'neutral']], 'attributes' => [['key'=>'speed','label'=>'Velocidad','value'=>94],['key'=>'finishing','label'=>'Finalización','value'=>89],['key'=>'stamina','label'=>'Resistencia','value'=>82],['key'=>'dribbling','label'=>'Regate','value'=>87]]],
            ['name' => 'Andrés Batista',  'number' => '08', 'position' => 'Volante',   'position_key' => 'midfielder', 'category' => 'first-team',  'nationality' => 'Panameño',   'height' => '1.76 m', 'dominant_foot' => 'Zurdo',   'biography' => 'Volante de formación. Andrés representa la cantera india con talento y compromiso.', 'sort_order' => 5],
            ['name' => 'José Murillo',    'number' => '19', 'position' => 'Delantero', 'position_key' => 'forward',    'category' => 'first-team',  'nationality' => 'Panameño',   'height' => '1.82 m', 'dominant_foot' => 'Derecho', 'biography' => 'Delantero de presión alta con velocidad y determinación en cada ataque.', 'sort_order' => 6],
            ['name' => 'Kevin Solís',     'number' => '14', 'position' => 'Defensa',   'position_key' => 'defender',   'category' => 'first-team',  'nationality' => 'Panameño',   'height' => '1.79 m', 'dominant_foot' => 'Derecho', 'biography' => 'Lateral derecho de banda con proyección ofensiva y disciplina defensiva.', 'sort_order' => 7],
            ['name' => 'Ronald Aguirre',  'number' => '12', 'position' => 'Portero',   'position_key' => 'goalkeeper', 'category' => 'first-team',  'nationality' => 'Panameño',   'height' => '1.87 m', 'dominant_foot' => 'Derecho', 'biography' => 'Segundo portero de gran seguridad. Ronald es el respaldo confiable del arco indio.', 'sort_order' => 8],
            // Women's team
            ['name' => 'Melissa Castillo','number' => '07', 'position' => 'Volante',   'position_key' => 'midfielder', 'category' => 'women-team',  'nationality' => 'Panameña',   'height' => '1.65 m', 'dominant_foot' => 'Derecho', 'biography' => 'Volante creativa del equipo femenino con técnica y visión de juego.', 'sort_order' => 1],
            ['name' => 'Natalia Vega',    'number' => '11', 'position' => 'Delantera', 'position_key' => 'forward',    'category' => 'women-team',  'nationality' => 'Panameña',   'height' => '1.62 m', 'dominant_foot' => 'Zurdo',   'biography' => 'Delantera veloz y goleadora del equipo femenino.', 'sort_order' => 2],
            ['name' => 'Camila Soto',     'number' => '05', 'position' => 'Defensa',   'position_key' => 'defender',   'category' => 'women-team',  'nationality' => 'Panameña',   'height' => '1.68 m', 'dominant_foot' => 'Derecho', 'biography' => 'Defensora central del equipo femenino, capitana del bloque defensivo.', 'sort_order' => 3],
            // Academy
            ['name' => 'Diego Palma',     'number' => '17', 'position' => 'Volante',   'position_key' => 'midfielder', 'category' => 'academy',     'nationality' => 'Panameño',   'height' => '1.72 m', 'dominant_foot' => 'Derecho', 'biography' => 'Joven promesa de la cantera india con gran proyección y disciplina táctica.', 'sort_order' => 1],
            ['name' => 'Fabián Cruz',     'number' => '23', 'position' => 'Delantero', 'position_key' => 'forward',    'category' => 'academy',     'nationality' => 'Panameño',   'height' => '1.75 m', 'dominant_foot' => 'Izquierdo', 'biography' => 'Delantero de cantera con velocidad y desequilibrio en los últimos metros.', 'sort_order' => 2],
        ];

        foreach ($players as $data) {
            Player::query()->updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'slug'    => Str::slug($data['name']),
                    'gallery' => null,
                ]),
            );
        }
    }
}
