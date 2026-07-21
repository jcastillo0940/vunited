<?php

namespace Database\Seeders;

use App\Models\Operator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    // OJO: nunca agregar WithoutModelEvents aqui - HasPublicUlid depende del
    // evento `creating` para generar public_id; con ese trait desactivado
    // los inserts fallan (public_id sin valor) de forma no obvia.
    public function run(): void
    {
        Operator::updateOrCreate(
            ['email' => 'admin@veraguasunited.test'],
            [
                'name' => 'Administrador Ticketing',
                'password' => Hash::make(bin2hex(random_bytes(16))),
                'role' => 'admin',
                'is_active' => true,
            ],
        );

        $this->call(LegacyEventsSeeder::class);
    }
}
