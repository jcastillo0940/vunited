<?php

namespace App\Models;

use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Support\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class Customer extends Authenticatable
{
    use HasApiTokens, HasPublicUlid, Notifiable;

    protected function casts(): array
    {
        return ['last_login_at' => 'datetime'];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
