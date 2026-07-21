<?php

namespace App\Domain\Ticketing\Models;

use App\Domain\Ticketing\Support\HasPublicUlid;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['zone_id', 'label', 'status'])]
class Seat extends Model
{
    use HasPublicUlid;

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }
}
