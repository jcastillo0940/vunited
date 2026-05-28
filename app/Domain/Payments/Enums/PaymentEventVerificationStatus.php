<?php

namespace App\Domain\Payments\Enums;

enum PaymentEventVerificationStatus: string
{
    case Pending  = 'pending';
    case Verified = 'verified';
    case Failed   = 'failed';
    case Skipped  = 'skipped'; // webhook_id not configured
}
