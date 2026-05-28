<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ticketing\Models\IssuedTicket;
use App\Domain\Ticketing\Models\TicketOrder;
use App\Domain\Ticketing\Services\TicketValidationService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IssuedTicketController extends Controller
{
    public function __construct(
        private readonly TicketValidationService $validationService,
    ) {}

    public function index(Request $request): View
    {
        $query = IssuedTicket::query()->with(['ticketOrder.matchEvent']);

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = trim($request->string('search')->toString())) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('token', 'like', "%{$search}%")
                    ->orWhere('seat_label', 'like', "%{$search}%")
                    ->orWhereHas('ticketOrder', function (Builder $q) use ($search): void {
                        $q->where('order_number', 'like', "%{$search}%")
                          ->orWhere('customer_email', 'like', "%{$search}%");
                    });
            });
        }

        return view('admin.issued-tickets.index', [
            'tickets' => $query->latest('issued_at')->get(),
            'filters' => [
                'status' => $request->string('status')->toString(),
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }

    public function show(IssuedTicket $issuedTicket): View
    {
        $issuedTicket->load(['ticketOrder.matchEvent', 'ticketOrderItem']);

        return view('admin.issued-tickets.show', [
            'ticket' => $issuedTicket,
        ]);
    }

    public function forOrder(TicketOrder $ticketOrder): View
    {
        $ticketOrder->load(['issuedTickets', 'matchEvent']);

        return view('admin.issued-tickets.index', [
            'tickets' => $ticketOrder->issuedTickets()->with('ticketOrder.matchEvent')->get(),
            'filters' => ['status' => '', 'search' => ''],
            'order'   => $ticketOrder,
        ]);
    }

    public function validateTicket(Request $request): JsonResponse
    {
        $request->validate(['token' => ['required', 'string', 'size:40']]);

        $result = $this->validationService->validate($request->string('token')->toString());

        $statusCode = $result['valid'] ? 200 : 422;

        return response()->json($result, $statusCode);
    }
}
