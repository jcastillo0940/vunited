<?php

namespace App\Domain\Store\Enums;

enum StoreOrderStatus: string
{
    case Draft = 'draft';
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
}
