<?php

namespace Database\Seeders;

use App\Domain\AdminUsers\Models\AdminUser;
use App\Domain\AccessControl\Models\Role;
use App\Domain\AccessControl\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permission = Permission::firstOrCreate(['name'=>'web.admin'], ['label'=>'Administración Web']);
        $role = Role::firstOrCreate(['name'=>'superadmin'], ['label'=>'Superadministrador']);
        $role->permissions()->syncWithoutDetaching([$permission->id]);
        $admin = AdminUser::firstOrCreate(['email'=>env('WEB_SEED_ADMIN_EMAIL','admin@veraguasunited.test')], ['name'=>'Web Superadministrador','password'=>Hash::make(env('WEB_SEED_ADMIN_PASSWORD','ChangeMe_123456!'))]);
        $admin->roles()->syncWithoutDetaching([$role->id]);
    }
}
