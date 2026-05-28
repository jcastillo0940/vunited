<?php

namespace App\Domain\Memberships\Enums;

enum MembershipOrderStatus: string
{
    case Draft          = 'draft';
    case PendingPayment = 'pending_payment';
    case Paid           = 'paid';
    case Cancelled      = 'cancelled';
    case Failed         = 'failed';
}
