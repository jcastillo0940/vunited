<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Models\ValidationEvent;
use Illuminate\Http\JsonResponse;

class ReportController
{
    public function events(): JsonResponse
    {
        $events = Event::query()
            ->withCount(['zones'])
            ->with('zones:id,event_id,capacity_total,capacity_available,capacity_held')
            ->orderByDesc('starts_at')
            ->get()
            ->map(fn (Event $event) => [
                'id' => $event->public_id,
                'code' => $event->code,
                'home_team' => $event->home_team,
                'away_team' => $event->away_team,
                'starts_at' => $event->starts_at?->toIso8601String(),
                'status' => $event->status,
                'capacity_total' => $event->zones->sum('capacity_total'),
                'capacity_sold' => $event->zones->sum(fn ($z) => $z->capacity_total - $z->capacity_available - $z->capacity_held),
                'capacity_held' => $event->zones->sum('capacity_held'),
            ]);

        return response()->json(['data' => $events]);
    }

    public function orders(): JsonResponse
    {
        $orders = Order::query()
            ->with('event:id,home_team,away_team')
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (Order $order) => [
                'id' => $order->public_id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'customer_email' => $order->customer_email,
                'total' => (float) $order->total,
                'event' => $order->event ? "{$order->event->home_team} vs {$order->event->away_team}" : null,
                'created_at' => $order->created_at?->toIso8601String(),
            ]);

        return response()->json(['data' => $orders]);
    }

    public function validations(): JsonResponse
    {
        $events = ValidationEvent::query()
            ->with(['ticket:id,public_id,zone_id', 'door:id,name', 'operator:id,name'])
            ->latest('occurred_at')
            ->limit(200)
            ->get()
            ->map(fn (ValidationEvent $v) => [
                'id' => $v->id,
                'result' => $v->result,
                'ticket_id' => $v->ticket?->public_id,
                'door' => $v->door?->name,
                'operator' => $v->operator?->name,
                'correlation_id' => $v->correlation_id,
                'occurred_at' => $v->occurred_at?->toIso8601String(),
            ]);

        return response()->json(['data' => $events]);
    }
}
