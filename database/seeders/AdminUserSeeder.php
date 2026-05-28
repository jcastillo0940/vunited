<?php

namespace Database\Seeders;

use App\Domain\AccessControl\Models\Role;
use App\Domain\AdminUsers\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = AdminUser::query()->updateOrCreate(
            ['email' => 'superadmin@veraguasunited.test'],
            [
                'name' => 'Veraguas United Superadmin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $role = Role::query()->where('name', 'superadmin')->first();

        if ($role) {
            $superadmin->roles()->syncWithoutDetaching([$role->id]);
        }
    }
}
