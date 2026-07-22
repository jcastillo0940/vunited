<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Signature('admins:create {email} {name} {--password=}')]
#[Description('Crea o actualiza un admin. Sin --password genera una contrasena aleatoria y la imprime UNA sola vez.')]
class CreateAdmin extends Command
{
    public function handle(): int
    {
        $password = $this->option('password') ?: Str::password(20);

        $admin = Admin::updateOrCreate(
            ['email' => $this->argument('email')],
            [
                'name' => $this->argument('name'),
                'password' => Hash::make($password),
                'is_active' => true,
            ],
        );

        $this->info("Admin {$admin->email} listo.");
        if (! $this->option('password')) {
            $this->warn("Contrasena generada (guardala ahora, no se vuelve a mostrar): {$password}");
        }

        return self::SUCCESS;
    }
}
