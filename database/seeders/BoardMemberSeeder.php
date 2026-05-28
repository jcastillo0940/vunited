<?php

namespace Database\Seeders;

use App\Domain\Board\Models\BoardMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BoardMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            // Presidency
            [
                'name'       => 'Ing. Ricardo Méndez',
                'role'       => 'Presidente Ejecutivo',
                'group'      => 'presidency',
                'biography'  => 'Con más de 20 años de experiencia en gestión empresarial y una pasión inquebrantable por el deporte veraguense, lidera el proyecto United con la convicción de profesionalizar cada área del club. Bajo su gestión, Veraguas United ha alcanzado hitos clave en infraestructura, cantera y sostenibilidad institucional.',
                'sort_order' => 1,
                'metadata'   => [
                    'title'          => 'Presidente Ejecutivo',
                    'primary_action' => ['label' => 'Ver trayectoria', 'href' => null],
                    'social_actions' => [
                        ['id' => 'share', 'label' => 'Compartir', 'icon' => 'share', 'href' => null],
                        ['id' => 'mail', 'label' => 'Correo', 'icon' => 'alternate_email', 'href' => null],
                    ],
                ],
            ],
            // Executive staff
            [
                'name'       => 'Carlos Villarreal',
                'role'       => 'Vicepresidente',
                'group'      => 'executive_staff',
                'biography'  => 'Coordina estructura operativa, matchday, viajes y relación institucional con sedes y proveedores.',
                'sort_order' => 1,
                'metadata'   => ['area' => 'Operaciones y Logística', 'tone' => 'primary', 'icons' => ['groups', 'mail']],
            ],
            [
                'name'       => 'Manuel Batista',
                'role'       => 'Director Deportivo',
                'group'      => 'executive_staff',
                'biography'  => 'Lidera scouting, metodología competitiva y la conexión entre primer equipo y semillero indio.',
                'sort_order' => 2,
                'metadata'   => ['area' => 'Gestión de Talento', 'tone' => 'accent', 'icons' => ['sports_soccer', 'mail']],
            ],
            [
                'name'       => 'Dra. Elena Ruiz',
                'role'       => 'Gerente General',
                'group'      => 'executive_staff',
                'biography'  => 'Supervisa gestión financiera, cumplimiento, gobierno corporativo y proyección institucional.',
                'sort_order' => 3,
                'metadata'   => ['area' => 'Administración Central', 'tone' => 'primary', 'icons' => ['description', 'mail']],
            ],
            // Board
            ['name' => 'J. Santamaría', 'role' => 'Vocal Principal', 'group' => 'board', 'sort_order' => 1, 'metadata' => ['category' => 'Junta Directiva']],
            ['name' => 'L. de Gracia',  'role' => 'Secretario',       'group' => 'board', 'sort_order' => 2, 'metadata' => ['category' => 'Junta Directiva']],
            ['name' => 'M. Castillo',   'role' => 'Tesorero',          'group' => 'board', 'sort_order' => 3, 'metadata' => ['category' => 'Junta Directiva']],
            ['name' => 'G. Pitti',      'role' => 'Vocal',             'group' => 'board', 'sort_order' => 4, 'metadata' => ['category' => 'Junta Directiva']],
            ['name' => 'R. Vega',       'role' => 'Vocal',             'group' => 'board', 'sort_order' => 5, 'metadata' => ['category' => 'Junta Directiva']],
            // Transparency / governance
            ['name' => 'Corporación Veraguas', 'role' => 'Socio Estratégico', 'group' => 'transparency', 'sort_order' => 1, 'metadata' => ['category' => 'Accionistas']],
            ['name' => 'F. Espinoza',          'role' => 'Asesor Legal',       'group' => 'transparency', 'sort_order' => 2, 'metadata' => ['category' => 'Gobernanza']],
            ['name' => 'H. Jiménez',           'role' => 'Marketing',          'group' => 'transparency', 'sort_order' => 3, 'metadata' => ['category' => 'Apoyo Ejecutivo']],
        ];

        foreach ($members as $data) {
            BoardMember::query()->updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'slug'      => Str::slug($data['name']),
                    'is_active' => true,
                    'biography' => $data['biography'] ?? null,
                    'email'     => $data['email'] ?? null,
                ]),
            );
        }
    }
}
