<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Store\Models\StoreOrder;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StoreOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = StoreOrder::query()->with('payment');

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

        return view('admin.store-orders.index', [
            'orders' => $query->latest()->get(),
            'filters' => [
                'status' => $request->string('status')->toString(),
                'search' => $request->string('search')->toString(),
            ],
        ]);
    }

    public function show(StoreOrder $storeOrder): View
    {
        $storeOrder->load(['items.product', 'payment.paymentEvents']);

        return view('admin.store-orders.show', [
            'order' => $storeOrder,
            'payment' => $storeOrder->payment,
            'safeMetadata' => $this->sanitize($storeOrder->metadata),
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
