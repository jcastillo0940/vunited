<?php

namespace App\Console\Commands;

use App\Models\Operator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Signature('operators:create {email} {name} {--role=admin} {--password=}')]
#[Description('Crea o actualiza un operador. Sin --password genera una contrasena aleatoria y la imprime UNA sola vez.')]
class CreateOperator extends Command
{
    public function handle(): int
    {
        $password = $this->option('password') ?: Str::password(20);

        $operator = Operator::updateOrCreate(
            ['email' => $this->argument('email')],
            [
                'name' => $this->argument('name'),
                'password' => Hash::make($password),
                'role' => $this->option('role'),
                'is_active' => true,
            ],
        );

        $this->info("Operador {$operator->email} ({$operator->role}) listo.");
        if (! $this->option('password')) {
            $this->warn("Contrasena generada (guardala ahora, no se vuelve a mostrar): {$password}");
        }

        return self::SUCCESS;
    }
}
