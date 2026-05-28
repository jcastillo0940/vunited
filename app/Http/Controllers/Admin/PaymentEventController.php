<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payments\Models\PaymentEvent;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PaymentEventController extends Controller
{
    public function index(Request $request): View
    {
        $query = PaymentEvent::query()->with('payment');

        if ($processingStatus = $request->string('processing_status')->toString()) {
            $query->where('processing_status', $processingStatus);
        }

        if ($search = trim($request->string('search')->toString())) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('provider_event_id', 'like', "%{$search}%")
                    ->orWhere('event_type', 'like', "%{$search}%")
                    ->orWhere('provider_order_id', 'like', "%{$search}%")
                    ->orWhere('provider_capture_id', 'like', "%{$search}%");
            });
        }

        return view('admin.payment-events.index', [
            'events' => $query->latest('received_at')->get(),
            'filters' => [
                'processing_status' => $request->string('processing_status')->toString(),
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }

    public function show(PaymentEvent $paymentEvent): View
    {
        $paymentEvent->load('payment');

        return view('admin.payment-events.show', [
            'event' => $paymentEvent,
            'safeHeaders' => $this->sanitize($paymentEvent->headers),
            'safePayload' => $this->sanitize($paymentEvent->payload),
        ]);
    }

    private function sanitize(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $sanitized = [];

        foreach ($value as $key => $item) {
            $normalizedKey = is_string($key) ? strtolower($key) : (string) $key;

            if (str_contains($normalizedKey, 'secret')
                || str_contains($normalizedKey, 'card')
                || str_contains($normalizedKey, 'cvv')
                || str_contains($normalizedKey, 'authorization')) {
                $sanitized[$key] = '***';
                continue;
            }

            $sanitized[$key] = is_array($item) ? $this->sanitize($item) : $item;
        }

        return $sanitized;
    }
}
