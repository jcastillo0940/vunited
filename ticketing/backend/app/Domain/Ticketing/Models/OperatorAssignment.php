<?php

namespace App\Domain\Ticketing\Models;

use App\Models\Operator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['operator_id', 'event_id', 'door_id'])]
class OperatorAssignment extends Model
{
    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function door(): BelongsTo
    {
        return $this->belongsTo(Door::class);
    }
}
