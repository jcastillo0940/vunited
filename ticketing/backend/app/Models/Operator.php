<?php

namespace App\Models;

use App\Domain\Ticketing\Models\Device;
use App\Domain\Ticketing\Models\OperatorAssignment;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class Operator extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(OperatorAssignment::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}
