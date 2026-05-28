<?php

namespace App\Domain\Payments\Enums;

enum PaymentEventProcessingStatus: string
{
    case Received  = 'received';
    case Processed = 'processed';
    case Ignored   = 'ignored';
    case Failed    = 'failed';
}
