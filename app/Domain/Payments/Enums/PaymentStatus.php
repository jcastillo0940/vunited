<?php

namespace App\Domain\Payments\Enums;

enum PaymentStatus: string
{
    case Pending         = 'pending';
    case ProviderCreated = 'provider_created';
    case Approved        = 'approved';
    case Captured        = 'captured';
    case Failed          = 'failed';
    case Cancelled       = 'cancelled';
    case Refunded        = 'refunded';
}
