<?php

namespace App\Jobs;

use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Services\TicketIssuingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class IssueTicketsForOrder implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [5, 15, 30, 60, 120];

    public function __construct(public readonly int $orderId) {}

    public function handle(TicketIssuingService $issuing): void
    {
        $order = Order::find($this->orderId);
        if (! $order) {
            Log::warning('tickets.issue_job_order_missing', ['order_id' => $this->orderId]);

            return;
        }

        // El servicio ya es idempotente (bloquea la orden y verifica
        // tickets existentes), asi que un reintento del job por timeout u
        // otra causa nunca duplica tickets.
        $issuing->issueForOrder($order);
    }
}
