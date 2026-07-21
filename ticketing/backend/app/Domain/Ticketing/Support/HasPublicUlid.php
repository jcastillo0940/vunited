<?php

namespace App\Domain\Ticketing\Support;

use Illuminate\Support\Str;

/**
 * Genera un ULID en `public_id` al crear el modelo, sin tocar la PK
 * autoincremental interna. El id autoincremental nunca se expone en la API
 * (evita enumeracion); public_id es lo unico que ve el cliente/QR/frontend.
 */
trait HasPublicUlid
{
    public static function bootHasPublicUlid(): void
    {
        static::creating(function ($model): void {
            if (empty($model->public_id)) {
                $model->public_id = (string) Str::ulid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }
}
