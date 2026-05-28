<?php

namespace App\Support\Audit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use JsonSerializable;

class AuditablePayload
{
    public static function from(Model|array|null $source): ?array
    {
        if ($source === null) {
            return null;
        }

        $payload = $source instanceof Model
            ? $source->attributesToArray()
            : $source;

        return self::normalize($payload);
    }

    /**
     * @param  mixed  $value
     */
    private static function normalize(mixed $value): mixed
    {
        if ($value instanceof Carbon) {
            return $value->toISOString();
        }

        if ($value instanceof JsonSerializable) {
            return $value->jsonSerialize();
        }

        if (! is_array($value)) {
            return $value;
        }

        $normalized = [];

        foreach ($value as $key => $item) {
            $normalized[$key] = self::normalize($item);
        }

        return $normalized;
    }
}
