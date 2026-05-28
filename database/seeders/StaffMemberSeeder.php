<?php

namespace Database\Seeders;

use App\Domain\Squad\Models\StaffMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StaffMemberSeeder extends Seeder
{
    public function run(): void
    {
        $staff = [
            ['name' => 'Gonzalo Méndez',   'role' => 'Director Técnico',        'category' => 'first-team', 'biography' => 'Liderando la visión táctica de los Indios con más de 15 años de experiencia internacional. Un estratega forjado en la disciplina y la victoria.', 'sort_order' => 1],
            ['name' => 'Ricardo Vega',     'role' => 'Asistente Técnico',       'category' => 'first-team', 'biography' => 'Brazo derecho del cuerpo técnico, Ricardo coordina el trabajo de campo y la preparación táctica de cada partido.', 'sort_order' => 2],
            ['name' => 'Marco Calderón',   'role' => 'Preparador Físico',       'category' => 'first-team', 'biography' => 'Especialista en acondicionamiento físico y rendimiento deportivo de alto nivel.', 'sort_order' => 3],
            ['name' => 'Iván Moreno',      'role' => 'Analista de Rendimiento', 'category' => 'first-team', 'biography' => 'Analista de datos y vídeo. Iván convierte estadísticas en ventajas tácticas para el club.', 'sort_order' => 4],
            ['name' => 'Patricia Ruiz',    'role' => 'Directora Técnica Femenino', 'category' => 'women-team', 'biography' => 'Responsable del crecimiento técnico y competitivo del equipo femenino de Veraguas United.', 'sort_order' => 1],
        ];

        foreach ($staff as $data) {
            StaffMember::query()->updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, ['slug' => Str::slug($data['name'])]),
            );
        }
    }
}
