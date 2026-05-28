<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Payments\Models\Payment;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PaymentMonitorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::query()->with('payable');

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = trim($request->string('search')->toString())) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder
                    ->where('provider_order_id', 'like', "%{$search}%")
                    ->orWhere('provider_capture_id', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        return view('admin.payments.index', [
            'payments' => $query->latest()->get(),
            'filters' => [
                'status' => $request->string('status')->toString(),
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }

    public function show(Payment $payment): View
    {
        $payment->load(['payable', 'paymentEvents']);

        return view('admin.payments.show', [
            'payment' => $payment,
            'safeMetadata' => $this->sanitize($payment->metadata),
            'safeProviderPayload' => $this->sanitize($payment->provider_payload),
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
