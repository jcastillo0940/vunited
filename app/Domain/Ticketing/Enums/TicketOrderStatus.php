<?php

namespace App\Domain\Ticketing\Enums;

enum TicketOrderStatus: string
{
    case Draft          = 'draft';
    case PendingPayment = 'pending_payment';
    case Paid           = 'paid';
    case Cancelled      = 'cancelled';
    case Failed         = 'failed';
}
