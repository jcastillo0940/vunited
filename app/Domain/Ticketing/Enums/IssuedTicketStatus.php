<?php

namespace App\Domain\Ticketing\Enums;

enum IssuedTicketStatus: string
{
    case Issued = 'issued';
    case Used   = 'used';
    case Voided = 'voided';
}
