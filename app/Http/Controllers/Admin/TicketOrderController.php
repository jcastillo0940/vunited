<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ticketing\Models\TicketOrder;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TicketOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = TicketOrder::query()->with(['matchEvent', 'payment']);

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = trim($request->string('search')->toString())) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        return view('admin.ticket-orders.index', [
            'orders'  => $query->latest()->get(),
            'filters' => [
                'status' => $request->string('status')->toString(),
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }

    public function show(TicketOrder $ticketOrder): View
    {
        $ticketOrder->load(['matchEvent', 'items.ticketZone', 'payment.paymentEvents', 'issuedTickets']);

        return view('admin.ticket-orders.show', [
            'order'   => $ticketOrder,
            'payment' => $ticketOrder->payment,
        ]);
    }
}
